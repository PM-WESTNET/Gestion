<?php

namespace app\modules\firstdata\models;

use Yii;
use app\components\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use app\modules\sale\models\Customer;
use app\modules\sale\models\bills\Bill;
use app\modules\firstdata\components\FirstdataExport as Export;

/**
 * This is the model class for table "firstdata_export".
 *
 * @property int $firstdata_export_id
 * @property int $created_at
 * @property string $file_url
 * @property int $firstdata_config_id
 * @property int $from_date
 * @property int $to_date
 * @property string $status
 *
 * @property CustomerHasFirstdataExport[] $customerHasFirstdataExports
 * @property FirstdataDebitHasExport[] $firstdataDebitHasExports
 * @property FirstdataCompanyConfig $firstdataConfig
 */
class FirstdataExport extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'firstdata_export';
    }

    public function behaviors() {

        return array_merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at']
                ]
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstdata_config_id', 'from_date', 'presentation_date', 'due_date'], 'required'],
            [['created_at', 'firstdata_config_id'], 'integer'],
            [['file_url'], 'string', 'max' => 255],
            [['firstdata_config_id'], 'exist', 'skipOnError' => true, 'targetClass' => FirstdataCompanyConfig::className(), 'targetAttribute' => ['firstdata_config_id' => 'firstdata_company_config_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'firstdata_export_id' => Yii::t('app', 'Firstdata Export'),
            'created_at' => Yii::t('app', 'Created At'),
            'file_url' => Yii::t('app', 'File Url'),
            'firstdata_config_id' => Yii::t('app', 'Company'),
            'presentation_date' => Yii::t('app', 'Presentation Date'),
            'due_date' => Yii::t('app', 'Due Date'),

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerHasFirstdataExports()
    {
        return $this->hasMany(CustomerHasFirstdataExport::className(), ['firstdata_export_id' => 'firstdata_export_id']);
    }

    public function getCustomers() 
    {
        return $this->hasMany(Customer::class, ['customer_id' => 'customer_id'])->viaTable('customer_has_firstdata_export', ['firstdata_export_id' => 'firstdata_export_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstdataDebitHasExports()
    {
        return $this->hasMany(FirstdataDebitHasExport::className(), ['firstdata_export_id' => 'firstdata_export_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstdataConfig()
    {
        return $this->hasOne(FirstdataCompanyConfig::className(), ['firstdata_company_config_id' => 'firstdata_config_id']);
    }

    // Formateo de fechas antes de guardar
    private function formatDatesBeforeSave() 
    {
        if ($this->from_date) {
            $this->from_date = strtotime(Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd'));
        }

        if ($this->to_date) {
            $this->to_date = strtotime(Yii::$app->formatter->asDate($this->to_date, 'yyyy-MM-dd'));
        }

        if ($this->presentation_date) {
            $this->presentation_date = strtotime(Yii::$app->formatter->asDate($this->presentation_date, 'yyyy-MM-dd'));
        }

        if ($this->due_date) {
            $this->due_date = strtotime(Yii::$app->formatter->asDate($this->due_date, 'yyyy-MM-dd'));
        }
    }

    // Formateo de Fechas luego de buscar
    private function formatDatesAfterFind() 
    {
        if ($this->from_date) {
            $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'dd-MM-yyyy');
        }

        if ($this->to_date) {
            $this->to_date = Yii::$app->formatter->asDate($this->to_date, 'dd-MM-yyyy');
            
        }

        if ($this->presentation_date) {
            $this->presentation_date = Yii::$app->formatter->asDate($this->presentation_date, 'dd-MM-yyyy');
        }

        if ($this->due_date) {
            $this->due_date = Yii::$app->formatter->asDate($this->due_date, 'dd-MM-yyyy');
        }
    }

    public function beforeSave($insert) 
    {
        if ($insert) {
            $this->status = 'draft';
        }
        $this->formatDatesBeforeSave();

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes) 
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $this->linkCustomers(); //Si estamos insertando, busco los clientes
        }
    }

    public function afterFind() 
    {
        parent::afterFind();

        $this->formatDatesAfterFind();
    }

    /*
        Busca y enlaza con la exportacion, los comprobantes correspondientes al rango de tiempo indicado
        El comprobante no debe estar en otra exportacion
    */
    public function linkCustomers() 
    {
        $customers = Customer::find()
            ->innerJoin('firstdata_automatic_debit fad', 'fad.customer_id=customer.customer_id')
            ->leftJoin('customer_has_firstdata_export chfe', 'chfe.customer_id=customer.customer_id')
            ->andWhere(['fad.company_config_id' => $this->firstdata_config_id])
            ->andWhere(['fad.status' => 'enabled'])
            ->andWhere(['>','customer.current_account_balance', 0])
            ->andWhere(['OR', ['IS', 'chfe.customer_id', null], ['<>', 'chfe.month', date('Y-m', $this->from_date)]])
            ->distinct()
            ->all();
 
        if ($customers) {
            $customersHasExport = [];

            foreach($customers as $customer) {
                $customersHasExport[] = [
                    $customer->customer_id,
                    $this->firstdata_export_id,
                    date('Y-m', $this->from_date)
                ];
            }

            // Usamos un batch insert para evitar insertar la relacion comprobante por comprobante
            Yii::$app->db->createCommand()
                ->batchInsert('customer_has_firstdata_export', ['customer_id', 'firstdata_export_id', 'month'], $customersHasExport)
                ->execute();

        }    

    }

    /**
     * Devuelve la suma del total de todos los comprobantes
     */
    public function getTotalImport()
    {
        $customers = $this->customers;
        $total = 0;

        foreach($customers as $customer) {
            $total += $customer->current_account_balance;
        }

        return $total;
    }

    /**
     * Genera el archivo para subir a Firstdata
     */
    public function export()
    {
        $filePath = Yii::getAlias('@app') . '/web/firstdataExports/'. $this->firstdata_export_id;

        if (!file_exists($filePath)) {
            mkdir($filePath, 0777);
        }

        $file = $filePath . '/DA168D.txt';

        $resource = fopen($file, 'w+');

        if (!$resource) {
            return false;
        }

        $resource = Export::createFile($this, $resource);

        fclose($resource);

        $this->updateAttributes([
            'status' => 'exported',
            'file_url' => $file
        ]);

        return true;
    }
}
