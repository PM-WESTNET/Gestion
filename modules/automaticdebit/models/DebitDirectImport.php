<?php

namespace app\modules\automaticdebit\models;

use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\checkout\models\Payment;
use app\modules\sale\models\Company;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\UploadedFile;

/**
 * This is the model class for table "debit_direct_import".
 *
 * @property int $debit_direct_import_id
 * @property string $file
 * @property int $import_timestamp
 * @property int $process_timestamp
 * @property int $status
 * @property int $company_id
 * @property int $bank_id
 * @property int $money_box_account_id
 * @property int $create_timestamp
 *
 * @property Bank $bank
 * @property Company $company
 * @property MoneyBoxAccount moneyBoxAccount
 * @property DebitDirectImportHasPayment[] $debitDirectImportHasPayments
 */
class DebitDirectImport extends \yii\db\ActiveRecord
{
    const DRAFT_STATUS = 1;
    const SUCCESS_STATUS = 10;

    public $fileUploaded;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'debit_direct_import';
    }


    public function behaviors()
    {
        return array_merge(parent::behaviors(),  [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_timestamp']
                ],
                'createdAtAttribute' => 'create_timestamp'
            ]
        ]); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['import_timestamp', 'process_timestamp', 'status', 'company_id', 'bank_id', 'money_box_account_id', 'create_timestamp'], 'integer'],
            [['company_id', 'bank_id', 'money_box_account_id',], 'required'],
            [['file'], 'string', 'max' => 255],
            //[['fileUploaded'], 'file', 'extensions' => 'txt'],
            [['bank_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bank::class, 'targetAttribute' => ['bank_id' => 'bank_id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'company_id']],
            [['money_box_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => MoneyBoxAccount::class, 'targetAttribute' => ['money_box_account_id' => 'money_box_account_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'debit_direct_import_id' => Yii::t('app', 'Debit Direct Import ID'),
            'file' => Yii::t('app', 'File'),
            'fileUploaded' => Yii::t('app', 'File'),
            'import_timestamp' => Yii::t('app', 'Import Timestamp'),
            'process_timestamp' => Yii::t('app', 'Process Timestamp'),
            'status' => Yii::t('app', 'Status'),
            'company_id' => Yii::t('app', 'Company ID'),
            'bank_id' => Yii::t('app', 'Bank ID'),
            'create_timestamp' => Yii::t('app', 'Create timestamp'),
            'money_box_account_id' => Yii::t('app', 'Money box account')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBank()
    {
        return $this->hasOne(Bank::class, ['bank_id' => 'bank_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['company_id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDebitDirectImportHasPayments()
    {
        return $this->hasMany(DebitDirectImportHasPayment::class, ['debit_direct_import_id' => 'debit_direct_import_id']);
    }

    public function getPayments()
    {
        return $this->hasMany(Payment::class, ['payment_id' => 'payment_id'])->viaTable('debit_direct_import_has_payment', ['debit_direct_import_id' => 'debit_direct_import_id']);
    }

    public function getFailedPayments()
    {
        return $this->hasMany(DebitDirectFailedPayment::class, ['import_id' => 'debit_direct_import_id']);
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * Importa el archivo y genera los pagos correspondientes.
     */
    public function import() {

        $bank_instance = $this->bank->getBankInstance();
        $file = UploadedFile::getInstance($this, 'fileUploaded');

        if (empty($file)) {
            $this->addError('fileUploaded', Yii::t('app','You must select a file'));
        }

        $fileName = Yii::getAlias('@app/web').'/direct_debit_import/'.$file->baseName.'.'.$file->extension;
        if(!file_exists(Yii::getAlias('@app/web').'/direct_debit_import/')) {
            mkdir(Yii::getAlias('@app/web').'/direct_debit_import/', 0777);
        }
        $file->saveAs($fileName);

        $resource = fopen($fileName, 'r');

        if ($resource === false){
            return false;
        }

        try {
            $result = $bank_instance->import($resource, $this, $fileName);
        }catch (\Exception $ex) {
            Yii::$app->session->addFlash('error', $ex->getMessage(). $ex->getTraceAsString());
            return [
                'status' => false,
                'errors' => $ex->getMessage(),
                'payments_created' => 0,
                'failed_payments' => 0,
                'failed_payments' => 0,
                'rejected_payment_register_created' => 0,
            ];
        }

        return [
            'status' => $result['status'],
            'errors' => $result['errors'],
            'payments_created' => $result['created_payments'],
            'failed_payments' => $result['failed_payments'],
            'rejected_payment_register_created' => $result['rejected_payment_register_created'],
        ];
    }

    /**
     * @param $payment_id
     * @return bool
     * Crea la relación entre el import y un pago a través del modelo DebitDirectImportHasPayment
     */
    public function createPaymentRelation($payment_id)
    {
        $ddihp = new DebitDirectImportHasPayment([
            'payment_id' => $payment_id,
            'debit_direct_import_id' => $this->debit_direct_import_id
        ]);

        return $ddihp->save();
    }

    /**
     * @return array
     * Cierra los pagos de una importación, de no poder cerrar un pago continúa, e informa en errors los pagos que no ha podido cerrar.
     */
    public function closePayments()
    {
        $payments = $this->getPayments()->andWhere(['status' => 'draft'])->all();
        $errors = [];

        foreach ($payments as $payment) {
            if (!$payment->close()){
                array_push($errors, Yii::t('app', "Can't close payment"). ': '.$payment->payment_id);
            }
        }

        if(empty($errors)) {
            $this->updateAttributes(['status' => DebitDirectImport::SUCCESS_STATUS]);
        }

        return [
            'status' => empty($errors) ? true : false,
            'errors' => $this->getErrorsAsString($errors)
        ];
    }

    /**
     * @param $errors
     * @return string
     * Devuelve un array de errores en un solo string con saltos de línea
     */
    private function getErrorsAsString($errors)
    {
        $error_string = '';
        foreach ($errors as $error) {
            $error_string .= "\n" . $error;
        }

        return $error_string;
    }

    /**
     * @param $customer_code
     * @param $amount
     * @param $date
     * @param $cbu
     * @param $import_id
     * @return bool
     * Crea un registro en de pago fallido y guarda la descripción del error
     */
    public static function createFailedPayment($customer_code, $amount, $date, $cbu, $import_id, $error)
    {
        $failed_payment = new DebitDirectFailedPayment([
            'customer_code' => $customer_code,
            'amount' => $amount,
            'date' => $date,
            'import_id' => $import_id,
            'cbu' => $cbu,
            'error' => $error
        ]);

        return $failed_payment->save(false);
    }

    /**
     * @return bool
     * Indica si la importación no tiene pagos pendientes para cerrar
     */
    public function arePaymentPendingToClose()
    {
        return $this->getPayments()->where(['status' => Payment::PAYMENT_DRAFT])->exists();
    }
    /**
     * @return bool
     * Returns true if ALL PAYMENTS from the import are draft still. making it possible to delete the import register and re-upload it.
     */
    public function areAllPaymentsPendingToClose()
    {
        $all_payments = $this->getPayments()->count();
        $draft_payments = $this->getPayments()->where(['status' => Payment::PAYMENT_DRAFT])->count();
        return ($all_payments == $draft_payments) ? true : false;
    }
}
