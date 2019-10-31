<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 8/06/18
 * Time: 15:44
 */

namespace app\modules\pagomiscuentas\models;

use app\modules\checkout\models\Payment;
use app\modules\checkout\models\PaymentItem;
use app\modules\config\models\Config;
use app\modules\pagomiscuentas\components\Cobranza\CobranzaReader;
use app\modules\pagomiscuentas\models\search\PagomiscuentasFileSearch;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use Yii;
use yii\web\UploadedFile;
use app\modules\sale\models\Bill;

/**
 * This is the model class for table "partner".
 *
 * @property integer $pagomiscuentas_file_id
 * @property string $type
 * @property string $date
 * @property string $file
 * @property string $path
 * @property string $status
 * @property integer $company_id
 * @property string $from_date
 *
 * @property PagomiscuentasHasBill[] $pagomiscuentasHasBills
 * @property PagomiscuentasHasPayment[] $pagomiscuentasHasPayments
 *
 */
class PagomiscuentasFile extends \app\components\companies\ActiveRecord
{
    const STATUS_DRAFT       = 'draft';
    const STATUS_CLOSED      = 'closed';

    const TYPE_PAYMENT       = 'payment';
    const TYPE_BILL          = 'bill';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pagomiscuentas_file';
    }

    public function rules(){

        return [
            [['company_id', 'date'],'required'],
            [['from_date'], 'required', 'when' => function ($model){ return $model->type === self::TYPE_BILL;}],
            [['status'], 'in', 'range' => ['draft', 'closed']],
            [['status'], 'default', 'value'=>'draft'],
            [['type'], 'in', 'range' => ['payment', 'bill']],
            [['date', 'from_date'], 'date'],
            [['from_date', 'total'], 'safe'],
            ['path', 'string'],
            ['file', 'unique'],
            [['file'],'file']
        ];

    }

    public function attributeLabels() {
        return [
            'pagomiscuentas_file_id' => Yii::t('pagomiscuentas','ID'),
            'file' => Yii::t('app','Company'),
            'date' => Yii::t('pagomiscuentas','Date'),
            'status' => Yii::t('pagomiscuentas', 'Status'),
            'type' => Yii::t('pagomiscuentas', 'Type')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagomiscuentasBills()
    {
        return $this->hasMany(PagomiscuentasFileHasBill::className(), ['pagomiscuentas_file_id' => 'pagomiscuentas_file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagomiscuentasPayments()
    {
        return $this->hasMany(PagomiscuentasFileHasPayment::className(), ['pagomiscuentas_file_id' => 'pagomiscuentas_file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * Devuelve los pagos generados por el archivo
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::class, ['payment_id' => 'payment_id'])->viaTable('pagomiscuentas_file_has_payment',['pagomiscuentas_file_id' => 'pagomiscuentas_file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * Devuelve los bill asociados a un archivo
     */
    public function getBills()
    {
        return $this->hasMany(Bill::class, ['bill_id' => 'bill_id'])->viaTable('pagomiscuentas_file_has_bill',['pagomiscuentas_file_id' => 'pagomiscuentas_file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * Devuelve los clientes a los que se les ha generado un pago.
     */
    public function getPaymentCustomer()
    {
        return $this->getPayments()->joinWith(Customer::tableName());
    }

    /**
     * @return \yii\db\ActiveQuery
     * Devuelve aquellos clientes a los que se les ha generado un pago y su empresa difiere de la empresa en la cual
     * se ha creado el archivo de pagomiscuentas
     */
    public function getCustomerInWrongCompany()
    {
        return $this->getPaymentCustomer()->where(['not',['customer.company_id' => $this->company_id]]);
    }

    public function beforeSave($insert) {
        if(parent::beforeSave($insert)){
            $this->formatDatesBeforeSave();
            return true;

        }else{
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->formatDatesAfterFind();
        parent::afterFind();
    }

    public function getDeletable()
    {
        if($this->type == self::TYPE_PAYMENT) {
            return ($this->getPagomiscuentasPayments()->count() == 0);
        } else {
            return ($this->getPagomiscuentasBills()->count() == 0);;
        }
    }

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
        $this->date = Yii::$app->formatter->asDate($this->date);
        if (!empty($this->from_date)){
            $this->from_date = Yii::$app->formatter->asDate($this->from_date);
        }
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {

        if (!empty($this->date)) {
            $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
        }

        if (!empty($this->from_date)) {
            $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd');
        }
    }

    public function upload()
    {
        $file = UploadedFile::getInstance($this, 'file');
        $folder = 'pagomiscuentas';
        if ($file) {
            $filePath = Yii::$app->params['upload_directory'] . "$folder/". uniqid('file') . '.' . $file->extension;

            if (!file_exists(Yii::getAlias('@webroot') . '/' . Yii::$app->params['upload_directory'] . "$folder/")) {
                mkdir(Yii::getAlias('@webroot') . '/' . Yii::$app->params['upload_directory'] . "$folder/", 0775, true);
            }

            $file->saveAs(Yii::getAlias('@webroot') . '/' . $filePath);

            $this->path = $filePath;
            $this->file = $file->name;

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     * Cierra el archivo. Crea los pagos o las relaciones con bill dependiendo de su tipo.
     * No permite el cerrado de un archivo que no esté en borrador
     */
    public function close()
    {
        if($this->status == self::STATUS_DRAFT) {
            if($this->type == self::TYPE_PAYMENT) {
                $this->closeImport();
            } else {
                $this->closeExport();
            }
        }
        return false;
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     * Crea los pagos y cierra el archivo
     */
    private function closeImport()
    {
        $cobranza = new CobranzaReader();
        $datas = $cobranza->parse($this);
        $payment_method_id = Config::getValue('pagomiscuentas-payment-method');

        // Guardo
        if($datas) {
            $trans = Yii::$app->db->beginTransaction();
            try {
                $total = 0;
                foreach ($datas as $data) {
                    $customer = Customer::findOne(['code' => $data['customer_id']]);
                    $date = (new \DateTime($data['fecha_cobro']))->format('Y-m-d');
                    $amount = ((float)$data['importe']) / 100;
                    $total = $total + $amount;
                    $payment = $this->createPayment($customer->customer_id, $date, $amount, $data['canal'], $payment_method_id);
                    $payment->applyToBill($data['bill_id']);
                    $this->createRelationWithPayment($payment->payment_id);
                    $payment->close();
                }

                $this->updateAttributes(['status' => self::STATUS_CLOSED, 'total' => $total]);
                $trans->commit();
                return true;
            }catch(\Exception $ex) {
                $trans->rollBack();
                Yii::debug($ex->getFile() . " - " .  $ex->getCode() . " - " . $ex->getTraceAsString() . " - " . $ex->getMessage());
                error_log($ex->getMessage());
                throw $ex;
            }
        }
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\Exception
     * Crea la relacion de bill y pagomiscuentas_file cambia el estado a pagomiscuentas_file.
     */
    private function closeExport()
    {
        try{
            $query = (new PagomiscuentasFileSearch())->findBills($this->pagomiscuentas_file_id);

            $transaction = Yii::$app->db->beginTransaction();
            $total = 0 ;

            foreach($query->batch(500) as $models){
                $this->batchPagomiscuentasFileHasBillInsert($models);

                foreach ($models as $model) {
                    $total = $total + $model['total'];
                }
            }

            $this->status = self::STATUS_CLOSED;
            $this->total = $total;
            $this->update(false);
            $transaction->commit();
            return true;

        } catch(\Exception $ex) {
            $transaction->rollBack();
            Yii::debug($ex->getFile() . " - " .  $ex->getCode() . " - " . $ex->getTraceAsString() . " - " . $ex->getMessage());
            error_log($ex->getMessage());
            throw $ex;
        }
    }

    /**
     * @param $payment_id
     * @return bool
     * Crea la relacion entre el arhivo y un pago
     */
    public function createRelationWithPayment($payment_id)
    {
        if($this->type == self::TYPE_PAYMENT) {
            $pfhp = new PagomiscuentasFileHasPayment();
            $pfhp->pagomiscuentas_file_id = $this->pagomiscuentas_file_id;
            $pfhp->payment_id = $payment_id;
            return $pfhp->save();
        }
        return false;
    }

    /**
     * @param $partner_distribution_model_id
     * @param $customer_id
     * @param $date
     * @param $amount
     * @param $item_description
     * @param $payment_method_id
     * @return bool
     * Crea un pago y su correspondiente item
     */
    public function createPayment($customer_id, $date, $amount, $item_description, $payment_method_id)
    {
        if($this->type == self::TYPE_PAYMENT) {
            $transaction = Yii::$app->db->beginTransaction();
            $payment = new Payment([
                'company_id' => $this->company_id,
                'partner_distribution_model_id' => $this->company->partner_distribution_model_id,
                'customer_id' => $customer_id,
                'status' => 'draft',
                'date' => $date,
                'amount' => $amount
            ]);

            if($payment->save()){
                $paymentItem = new PaymentItem([
                    'payment_id' => $payment->payment_id,
                    'amount' => $amount,
                    'description' => $item_description,
                    'payment_method_id' => $payment_method_id
                ]);
                if($paymentItem->save()) {
                    $transaction->commit();
                    return $payment;
                }
                $transaction->rollBack();
                return false;
            }
        }
        return false;
    }

    /**
     * @param $models
     * @throws \yii\db\Exception
     * Inserta por lotes los modelos de la relacion PagomiscuentasFileHasBill.
     */
    private function batchPagomiscuentasFileHasBillInsert($models)
    {
        $data = [];
        foreach ($models as $bill) {

            $data[] = [
                $this->pagomiscuentas_file_id,
                $bill['bill_id']
            ];

        }

        Yii::$app->db->createCommand()->batchInsert('pagomiscuentas_file_has_bill', ['pagomiscuentas_file_id', 'bill_id'], $data)->execute();
    }
}