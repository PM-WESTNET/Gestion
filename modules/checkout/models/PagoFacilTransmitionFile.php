<?php

namespace app\modules\checkout\models;

use app\components\db\ActiveRecord;
use app\components\helpers\EmptyLogger;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\checkout\components\PagoFacilReader;
use app\modules\sale\models\Customer;
use Yii;
use yii\db\Expression;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "pago_facil_transmition_file".
 *
 * @property integer $pago_facil_transmition_file_id
 * @property string $header_file
 * @property string $upload_date
 * @property integer $money_box_account_id
 * @property double $total  
 * @property File $file
 * @property integer $money_box
 * @property string $file_name
 * @property string $status
 */
class PagoFacilTransmitionFile extends ActiveRecord {


    const STATUS_DRAFT = 'draft';
    const STATUS_CLOSED = 'closed';

    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'pago_facil_transmition_file';
    }

    /**
     * @inheritdoc
     */
    /*
      public function behaviors()
      {
      return [
      'timestamp' => [
      'class' => 'yii\behaviors\TimestampBehavior',
      'attributes' => [
      yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
      ],
      ],
      'date' => [
      'class' => 'yii\behaviors\TimestampBehavior',
      'attributes' => [
      yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
      ],
      'value' => function(){return date('Y-m-d');},
      ],
      'time' => [
      'class' => 'yii\behaviors\TimestampBehavior',
      'attributes' => [
      yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
      ],
      'value' => function(){return date('h:i');},
      ],
      ];
      }
     */

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['money_box_account_id', 'money_box_id'], 'required'],
            [['upload_date', 'header_file'], 'safe'],
            [['upload_date'], 'date'],
            [['header_file'], 'string', 'max' => 256],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => '900'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'pago_facil_transmition_file_id' => 'Idpago Facil Transmition File',
            'header_file' => 'Header File',
            'upload_date' => Yii::t('app', 'Upload Date'),
            'file' => Yii::t('app', 'File'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->formatDatesBeforeSave();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        $this->formatDatesAfterFind();
        parent::afterFind();
    }

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind() {
        $this->upload_date = Yii::$app->formatter->asDate($this->upload_date);
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave() {
        $this->upload_date = Yii::$app->formatter->asDate($this->upload_date, 'yyyy-MM-dd');
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable() {
        if($this->status == self::STATUS_DRAFT && (!$this->getPayments()->exists())) {
            return true;
        }

        return false;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
     */
    protected function unlinkWeakRelations() {
        
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            if ($this->getDeletable()) {
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    public function getMoneyBoxAccount()
    {
        return $this->hasOne(MoneyBoxAccount::class, ['money_box_account_id' => 'money_box_account_id']);
    }

    /**
     * Verifica si el archivo a importar ha sido importado antes 
     * @return boolean
     */
    public function isRepeat() {
        $file = PagoFacilTransmitionFile::findOne(['file_name' => $this->file_name]);
        
        if (empty($file)) {
            return false;
        } else {
            Yii::$app->session->setFlash('error', 'El archivo seleccionado ya ha sido importado antes. Seleccione otro y reintente.');
            return true;
        }
    }

    /**
     * Recorre el archivo de pago facil cargado y genera los payments correspondientes
     * @return boolean
     */
    public function import() {

        Yii::setLogger(new EmptyLogger());
        if (empty($this->money_box_account_id) && empty($this->money_box)) {
            Yii::$app->session->setFlash('error', 'Seleccione el banco y/o la cuenta destinatarios');
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction(); // Inicia transaccion con la base de datos
        try {
            $array_data = PagoFacilReader::parse($this);
            $moneyBoxAccount = MoneyBoxAccount::findOne(['money_box_account_id' => $this->money_box_account_id]);
            $paymentMethod = PaymentMethod::find()->where("status='enabled' AND lower(name) = '".strtolower("Pago Facil")."'")->one();

            $newPayments = $this->createPayments($array_data, $moneyBoxAccount, $paymentMethod);
            if($newPayments['status']) {
                $transaction->commit(); // Finaliza transaccion
                return [
                    'status' => true,
                    'errors' => $newPayments['errors']
                ];
            } else {
                $transaction->rollBack();
                return [
                    'status' => false,
                    'errors' => $newPayments['errors']
                ];
            }
        } catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', $ex->getMessage());
        }
    }

    /**
     * @param $array_data
     * @param $moneyBoxAccount
     * @param $paymentMethod
     * @return bool
     * Crea Payments, PaymentItems y PagofacilPayments. Actualiza el total del archivo
     */
    private function createPayments($array_data, $moneyBoxAccount, $paymentMethod) {
        $paymentItem = [];
        $pagofacilPayment = [];
        $count = 0;
        $total = 0;
        $errors = [];

        foreach ($array_data as $data) {
            $customer = Customer::find()->where(['customer.code' => $data['customer_id']])->one();

            if (!$customer) {
                array_push($errors, Yii::t('app', 'Customer not found: ') . $data['customer_id']);
            } else {
                $payment_id = $this->createPayment($customer->company_id, $customer->customer_id, "PAGO FACIL", $data['date'], $data['amount'], $customer->company->partner_distribution_model_id, $data);
                if(!$payment_id) {
                    array_push($errors, "Error al guardar el pago del cliente $customer->code. Reintente");
                }

                //actualizo el valor en el array
                $data['payment_id'] = $payment_id;

                //Si el medio de pago es diferente al de pago facil, busco nuevamente.
                if(strtolower($data['payment_method']) != strtolower("Pago Facil")) {
                    $paymentMethod = PaymentMethod::find()->where("status='enabled' AND lower(name) = '".strtolower($array_data['payment_method'])."'")->one();
                }

                $paymentItem[] = [$payment_id, $data['amount'], "PAGO FACIL", $paymentMethod->payment_method_id, $moneyBoxAccount ? $moneyBoxAccount->money_box_account_id : '' ];
                $pagofacilPayment[] = [$this->pago_facil_transmition_file_id, $payment_id];
                $total += $data['amount'];
            }

        }
        $this->batchPaymentItemInsert($paymentItem);
        $this->batchPagoFacilPaymentInsert($pagofacilPayment);

        $this->updateAttributes(['total' => $total]);
        return [
            'status' => $errors ? false : true,
            'errors' => $errors
        ];
    }

    /**
     * @param $company_id
     * @param $customer_id
     * @param $concept
     * @param $date
     * @param $amount
     * @param $partner_distribution_model_id
     * @param $data
     * @return bool|int
     * Crea un pago y devuelve el payment_id
     */
    private function createPayment($company_id, $customer_id, $concept, $date, $amount, $partner_distribution_model_id, &$data)
    {
        $payment = new Payment([
            'company_id' => $company_id,
            'customer_id' => $customer_id,
            'concept' => $concept,
            'date' => $date,
            'amount' => $amount,
            'partner_distribution_model_id' => $partner_distribution_model_id,
        ]);

        if($payment->save()) {
            return $payment->payment_id;
        }

        return false;
    }

    /**
     * @param $data
     * @throws \yii\db\Exception
     * Inserta por lotes PaymentItems
     */
    private function batchPaymentItemInsert($data)
    {
        Yii::$app->db->createCommand()->batchInsert('payment_item', ['payment_id', 'amount', 'description', 'payment_method_id', 'money_box_account_id'], $data)->execute();
    }

    /**
     * @param $data
     * @throws \yii\db\Exception
     * Inserta por lotes PagoFacilPayments
     */
    private function batchPagoFacilPaymentInsert($data)
    {
        Yii::$app->db->createCommand()->batchInsert('pago_facil_payment', ['pago_facil_transmition_file_pago_facil_transmition_file_id', 'payment_payment_id'], $data)->execute();
    }
    
    /**
     * 
     * @return ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(PagoFacilPayment::class, ['pago_facil_transmition_file_pago_facil_transmition_file_id' => 'pago_facil_transmition_file_id']);
    }
    
    public function payments(){
        return $this->getPayments();
    }
    
    public function confirmFile(){
        $payments = $this->payments;
        
        foreach ($payments as $payment){
            $payment->paymentPayment->close();           
        }
        
        $this->status = 'closed';
        $this->updateAttributes(['status']);
        return true;
    }

}
