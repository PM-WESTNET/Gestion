<?php

namespace app\modules\sale\models;

use app\modules\pagomiscuentas\models\PagomiscuentasFile;
use app\modules\sale\modules\invoice\components\Invoice;
use Yii;
use app\modules\sale\models\BillType;
use app\modules\sale\models\Company;

/**
 * This is the model class for table "invoice_process".
 *
 * @property int $invoice_process_id
 * @property string $start_datetime
 * @property string $end_datetime
 * @property int $company_id
 * @property int $bill_type_id
 * @property string $period
 * @property string $from_date
 * @property string $to_date
 * @property string $status
 * @property string $observation
 * @property string $type
 *
 * @property BillType $billType
 * @property Company $company
 */
class InvoiceProcess extends \yii\db\ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_ERROR = 'error';
    const STATUS_FINISHED = 'finished';
    const STATUS_PAUSED = 'paused';
    

    const TYPE_CREATE_BILLS = 'create_bills';
    const TYPE_CLOSE_BILLS = 'close_bills';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_process';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'bill_type_id', 'status', 'type'],'required'],
            [['start_datetime', 'end_datetime'], 'safe'],
            [['company_id', 'bill_type_id'], 'integer'],
            [['status', 'observation', 'type'], 'string'],
            [['period', 'from_date', 'to_date'], 'string', 'max' => 255],
            ['period', 'required', 'when' => function($model) {
                return $model->type == InvoiceProcess::TYPE_CREATE_BILLS;
            }],
            [['bill_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => BillType::class, 'targetAttribute' => ['bill_type_id' => 'bill_type_id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'company_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'invoice_process_id' => Yii::t('app', 'Invoice Process ID'),
            'start_datetime' => Yii::t('app', 'Start Datetime'),
            'end_datetime' => Yii::t('app', 'End Datetime'),
            'company_id' => Yii::t('app', 'Company ID'),
            'bill_type_id' => Yii::t('app', 'Bill Type ID'),
            'period' => Yii::t('app', 'Period'),
            'status' => Yii::t('app', 'Status'),
            'observation' => Yii::t('app', 'Observation'),
            'type' => Yii::t('app', 'Type'),
            'from_date' => Yii::t('app', 'From date'),
            'to_date' => Yii::t('app', 'To date'),
        ];
    }

    public function behaviors()
    {
        return [
            'start_datetime' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['start_datetime'],
                ],
                'value' => function(){
                    return (new \DateTime('now'))->getTimestamp();
                }
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillType()
    {
        return $this->hasOne(BillType::class, ['bill_type_id' => 'bill_type_id']);
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
    public function getBills()
    {
        return $this->hasMany(Bill::class, ['invoice_process_id' => 'invoice_process_id']);
    }

    public static function createInvoiceProcess($company_id, $bill_type_id, $period = null, $observation, $type, $from_date = null, $to_date = null)
    {
        if(!InvoiceProcess::getPendingInvoiceProcess($type)) {
            $model = new InvoiceProcess([
                'company_id' => $company_id,
                'bill_type_id' => $bill_type_id,
                'status' => InvoiceProcess::STATUS_PENDING,
                'period' => $period,
                'observation' => $observation,
                'type' => $type,
                'from_date' => $from_date,
                'to_date' => $to_date
            ]);

//            return
                $a = $model->save();
            \Yii::trace($model->getErrors());
            return $a;
        }

        return false;
    }

    public static function getPendingInvoiceProcess($type)
    {
        return InvoiceProcess::find()->where(['status' => InvoiceProcess::STATUS_PENDING, 'type' => $type])->one();
    }

    public static function getPausedInvoiceProcess($type)
    {
        return InvoiceProcess::find()->where(['status' => InvoiceProcess::STATUS_PAUSED, 'type' => $type])->one();
    }

    public static function endProcess($type)
    {
        $invoice_process = InvoiceProcess::getPendingInvoiceProcess($type);

        if($invoice_process) {
            $invoice_process->status = InvoiceProcess::STATUS_FINISHED;
            $invoice_process->end_datetime = (new \DateTime('now'))->getTimestamp();
            return $invoice_process->save();
        }

        return false;
    }

    /**
    * Cambia el estado del proceso a pausado
    */
    public static function pauseProcess($type){
        $invoice_process = InvoiceProcess::getPendingInvoiceProcess($type);

        if($invoice_process) {
            $invoice_process->status = InvoiceProcess::STATUS_PAUSED;
            return $invoice_process->save();
        }

        return false;
    }

    /**
    * Cambia el estado del proceso a pendiente
    */
    public static function pendingProcess($type){
        $invoice_process = InvoiceProcess::getPausedInvoiceProcess($type);

        if($invoice_process) {
            $invoice_process->status = InvoiceProcess::STATUS_PENDING;
            return $invoice_process->save();
        }

        return false;
    }

    /**
     * Crea un registro de de la tabla pagomiscuentas_file con la
     */
    public function createPagoMisCuentasFile()
    {
        //Verifico si el registro existe
        $pmc_file_exists = PagomiscuentasFile::find()->where(['created_by_invoice_process_id' => $this->invoice_process_id])->exists();
        if($pmc_file_exists) {
            return true;
        }

        $model = new PagomiscuentasFile([
            'from_date' => (new \DateTime())->setTimestamp($this->start_datetime)->format('d-m-Y'),
            'date' => (new \DateTime())->setTimestamp($this->start_datetime)->format('d-m-Y'),
            'company_id' => $this->company_id,
            'type' => PagomiscuentasFile::TYPE_BILL,
            'status' => PagomiscuentasFile::STATUS_DRAFT,
            'created_by_invoice_process_id' => $this->invoice_process_id
        ]);

        return $model->save();
    }

    /**
     * Cierra el registro de pagomiscuentas si es que tiene uno asociado.
     */
    public function closePMCAssociatedFile()
    {
        $pmc_file = PagomiscuentasFile::find()->where(['created_by_invoice_process_id' => $this->invoice_process_id])->one();

        if($pmc_file) {
            $pmc_file->updateAttributes(['date' => (new \DateTime('now'))->format('Y-m-d')]);
            return $pmc_file->close();
        }

        return false;
    }

    /**
     * Retorna un string con una descripción breve de los datos mas importantes del proceso de facturación para una
     * identificación rápida del mismo
     */
    public function getDescriptiveName()
    {
        return 'Proceso de facturación Nº ' . $this->invoice_process_id .' del '.$this->period;
    }
}
