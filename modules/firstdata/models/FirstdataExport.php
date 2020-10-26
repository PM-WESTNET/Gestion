<?php

namespace app\modules\firstdata\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use app\modules\sale\models\Customer;
use app\modules\sale\models\bills\Bill;

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
 * @property BillHasFirstdataExport[] $billHasFirstdataExports
 * @property FirstdataDebitHasExport[] $firstdataDebitHasExports
 * @property FirstdataCompanyConfig $firstdataConfig
 */
class FirstdataExport extends \yii\db\ActiveRecord
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
            [['firstdata_config_id', 'from_date'], 'required'],
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
            'firstdata_export_id' => Yii::t('app', 'Firstdata Export ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'file_url' => Yii::t('app', 'File Url'),
            'firstdata_config_id' => Yii::t('app', 'Firstdata Config ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillHasFirstdataExports()
    {
        return $this->hasMany(BillHasFirstdataExport::className(), ['firstdata_export_id' => 'firstdata_export_id']);
    }

    public function getBills() 
    {
        return $this->hasMany(Bill::class, ['bill_id' => 'bill_id'])->viaTable('bill_has_firstdata_export', ['firstdata_export_id' => 'firstdata_export_id']);
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

    private function formatDatesBeforeSave() 
    {
        if ($this->from_date) {
            $this->from_date = strtotime(Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd'));
        }

        if ($this->to_date) {
            $this->to_date = strtotime(Yii::$app->formatter->asDate($this->to_date, 'yyyy-MM-dd'));
        }
    }

    private function formatDatesAfterFind() 
    {
        if ($this->from_date) {
            $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'dd-MM-yyyy');
        }

        if ($this->to_date) {
            $this->to_date = Yii::$app->formatter->asDate($this->to_date, 'dd-MM-yyyy');
            
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
            $this->linkBills();
        }
    }

    public function afterFind() 
    {
        parent::afterFind();

        $this->formatDatesAfterFind();
    }

    public function linkBills() 
    {
        $customers = Customer::find()
            ->innerJoin('firstdata_automatic_debit fad', 'fad.customer_id=customer.customer_id')
            ->andWhere(['fad.company_config_id' => $this->firstdata_config_id])
            ->andWhere(['fad.status' => 'enabled'])
            ->all();

        if ($customers) {
            $customersIds = array_map(function($customer){ return $customer->customer_id; }, $customers);

            $billsQuery = Bill::find()
                ->leftJoin('bill_has_firstdata_export bhfe', 'bhfe.bill_id=bill.bill_id')
                ->andWhere(['IN', 'customer_id', $customersIds])
                ->andWhere(['status' => Bill::STATUS_CLOSED])
                ->andWhere(['IS', 'bhfe.bill_has_firstdata_export_id', NULL])
                ->andWhere(['>=', 'timestamp', $this->from_date]);

            if ($this->to_date) {
                $billsQuery->andWhere(['<', 'timestamp', ($this->to_date + 86400)]);
            }
            
            $bills = $billsQuery->all();
            $billsHasExport = [];

            foreach($bills as $bill) {
                $billsHasExport[] = [
                    $bill->bill_id,
                    $this->firstdata_export_id
                ];
            }

            Yii::$app->db->createCommand()
                ->batchInsert('bill_has_firstdata_export', ['bill_id', 'firstdata_export_id'], $billsHasExport)
                ->execute();

        }    

    }
}
