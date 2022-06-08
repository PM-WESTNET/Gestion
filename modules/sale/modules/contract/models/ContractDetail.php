<?php

namespace app\modules\sale\modules\contract\models;

use app\components\db\ActiveRecord;
use app\components\helpers\EmptyLogger;
use app\modules\sale\models\Discount;
use app\modules\sale\models\FundingPlan;
use app\modules\sale\models\Product;
use app\modules\sale\models\ProductToInvoice;
use app\modules\westnet\models\Vendor;
use DateTime;
use webvimark\modules\UserManagement\models\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Application;


/**
 * This is the model class for table "contract_detail".
 *
 * @property integer $contract_detail_id
 * @property integer $contract_id
 * @property integer $product_id
 * @property string $date
 * @property string $from_date
 * @property string $to_date
 * @property string $status
 * @property integer $funding_plan_id
 * @property integer $discount_id
 * @property integer $applied
 * @property float $count
 *
 * @property Contract $contract
 * @property FundingPlan $fundingPlan
 * @property Product $product
 * @property ContractDetailLog[] $contractDetailLogs
 * @property ProductToInvoice[] $productToInvoices
 * @property Discount $discount
 *
 */
class ContractDetail extends ActiveRecord
{

    const STATUS_ACTIVE = 'active';
    const STATUS_DRAFT = 'draft';
    const STATUS_CANCELLED = 'canceled';
    const STATUS_LOW = 'low';
    const STATUS_LOW_PROCESS = 'low-process';

    public $tmp_discount_id;

    public function __construct($config = array()) {
        parent::__construct($config);

        $this->count = 1;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contract_detail';
    }
    
    public function init() {
        parent::init();

        $vendor = null;
        if(Yii::$app instanceof Application) {
            $vendor = Vendor::findByUserId(Yii::$app->user->id);
        }

        $this->vendor_id = $vendor ? $vendor->vendor_id : NULL;
    }

    /**
     * @inheritdoc
     */
    /*  public function behaviors()
    {

        return [

        ];
    }*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contract_id', 'product_id', 'count', 'vendor_id'], 'required'],
            [['contract_id', 'product_id', 'funding_plan_id', 'discount_id', 'tmp_discount_id', 'applied'], 'integer'],
            [['date', 'from_date', 'to_date', 'contract', 'fundingPlan', 'product', 'discount', 'tmp_discount_id'], 'safe'],
            [['count',], 'double', 'min' => 0],
            [['date', 'from_date', 'to_date'], 'date'],
            [['status'], 'string'],
            [['status'], 'in', 'range' => ['draft', 'active', 'canceled', 'low', 'low-process']],
            [['status'], 'default', 'value' => 'draft'],
            [['applied'], 'default', 'value' => '0'],
            [['funding_plan_id', 'discount_id'], 'default', 'value' => null],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'contract_id' => Yii::t('app', 'Contract'),
            'product_id' => Yii::t('app', 'Plan'),
            'date' => Yii::t('app', 'Date'),
            'from_date' => Yii::t('app', 'From Date'),
            'to_date' => Yii::t('app', 'To Date'),
            'status' => Yii::t('app', 'Status'),
            'funding_plan_id' => Yii::t('app', 'Funding Plan'),
            'contract' => Yii::t('app', 'Contract'),
            'fundingPlan' => Yii::t('app', 'Funding Plan'),
            'product' => Yii::t('app', 'Product'),
            'contractDetailLogs' => Yii::t('app', 'ContractDetailLogs'),
            'discount_id'  => Yii::t('app', 'Discount'),
            'vendor_id' => Yii::t('westnet', 'Vendor'),
            'applied'  => Yii::t('app', 'Applied'),
            'count' => \Yii::t('app', 'Count'),
        ];
    }    


    /**
     * @return ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Contract::className(), ['contract_id' => 'contract_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFundingPlan()
    {
        return $this->hasOne(FundingPlan::className(), ['funding_plan_id' => 'funding_plan_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContractDetailLogs()
    {
        return $this->hasMany(ContractDetailLog::className(), ['contract_detail_id' => 'contract_detail_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductToInvoices()
    {
        return $this->hasMany(ProductToInvoice::className(), ['contract_detail_id' => 'contract_detail_id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getDiscount()
    {
        return $this->hasOne(Discount::className(), ['discount_id' => 'discount_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductToInvoicesForPeriod($period)
    {
        if (!$period){
            $period = new DateTime('now');
        } else {
            $period = new DateTime($period);
        }
        return $this->hasMany(ProductToInvoice::className(),[
            'contract_detail_id' => 'contract_detail_id',
            'date_format(period, \'%Y%m\')'=>$period->format('Ym'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            
            $this->formatDatesBeforeSave();
            return true;
        } else {
            return false;
        }
    }

    // todo change in future case the 'plan fibra' condition to be inside the function updateOnIsp()
    // public function afterSave($insert, $changedAttributes) {
    //     parent::afterSave($insert, $changedAttributes);
    //     if(isset($changedAttributes['product_id'])){
    //         if($this->product->isProductCategory('Plan fibra')){
    //             // update on ISP ()
    //             $updated = $this->contract->updateOnISP();
    //             if(Yii::$app->session && !$updated) {
    //                 Yii::$app->session->addFlash('error', 'Plan fibra no se actualizó correctamente en ISP');
    //             }
    //         }    
    //     }
    // }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->formatDatesAfterFind();
        parent::afterFind();
    }

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
        if(empty($this->to_date) || $this->to_date == Yii::t('app', 'Undetermined time')){
            $this->to_date = Yii::t('app', 'Undetermined time');
        }else{
            $this->to_date = Yii::$app->formatter->asDate($this->to_date, 'dd-MM-yyyy');
        }
        if(empty($this->from_date) || $this->from_date == Yii::t('app', 'Undetermined time')){
            $this->from_date = Yii::t('app', 'Undetermined time');
        }else{
            $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'dd-MM-yyyy');
        }
        $this->date = Yii::$app->formatter->asDate($this->date);
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
        if(empty($this->from_date) || $this->from_date == Yii::t('app', 'Undetermined time')){
            $this->from_date = null;
        }else{
            $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd');
        }
        if(empty($this->to_date) || $this->to_date == Yii::t('app', 'Undetermined time')){
            $this->to_date = null;
        }else{
            $this->to_date = Yii::$app->formatter->asDate($this->to_date, 'yyyy-MM-dd');
        }
        if (empty($this->date)) {
            $this->date = (new DateTime('now'))->format('Y-m-d');
        } else {
            $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
        }
    }

                 
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return ($this->status=='draft' );
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Contract, Product.
     */
    protected function unlinkWeakRelations()
    {
        if($this->status == self::STATUS_DRAFT) {
            ContractDetailLog::deleteAll(['contract_detail_id' => $this->contract_detail_id]);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Comparo si los dos modelos son iguales.
     * @param ContractDetail $oldContractDetail
     * @return bool
     */
    public function isEqual(ContractDetail $oldContractDetail)
    {
        return !( $this->contract_detail_id == $oldContractDetail->contract_detail_id && (
                $this->funding_plan_id != $oldContractDetail->funding_plan_id ||
                $this->product_id != $oldContractDetail->product_id ||
                $this->discount_id != $oldContractDetail->discount_id ||
                $this->date != $oldContractDetail->date ||
                $this->from_date != $oldContractDetail->from_date ||
                $this->to_date != $oldContractDetail->to_date ||
                $this->status != $oldContractDetail->status ) );
    }

    /**
     * Crea un archivo de log con sus propios datos
     * @return bool
     */
    public function createLog($event=null)
    {
        if ($this->status=='draft') {
            return false;
        }
        $log = new ContractDetailLog();
        $log->contract_detail_id = $this->contract_detail_id;
        $log->funding_plan_id = $this->funding_plan_id;
        $log->product_id = $this->product_id;
        $log->date = $this->date;
        $log->from_date = $this->from_date;
        $log->to_date = $this->to_date;
        $log->status = $this->status;
        $log->discount_id = $this->discount_id;
        $log->save(false);
        return true;
    }

    /**
     * Verifica si se ha generado el registro ProductToBill para el periodo pasado como parametro.
     * En caso de que el parametro venga nulo, se utiliza el corriente mes.
     *
     * @param null $period
     * @return boolean
     */
    public function isAddedForInvoice($periods=null)
    {
        $finalPeriods = [];
        if(is_null($periods)){
            $finalPeriods[] = new DateTime('now');
        } else {
            if(is_array($periods)){
                foreach($periods as $period) {
                    if (!$period instanceof DateTime) {
                        $finalPeriods[] = new DateTime($period);
                    } else {
                        $finalPeriods[] = $period;
                    }
                }
            }
        }

        $query = (new Query())
                ->from('product_to_invoice');
        $where = ['and', 'contract_detail_id=:contract_detail_id', "status='active'"];

        $aPeriods[] = 'or';
        foreach($finalPeriods as $period) {
            $aPeriods[] = "date_format(period, '%Y%m') = '" . $period->format('Ym') ."'";
        }
        $where[] = $aPeriods;
        $query->where($where, [':contract_detail_id'=>$this->contract_detail_id]);

        return ($query->count() > 0);
    }

    /**
     * Verifica si se ha facturado el periodo pasado como parametro.
     * En caso de que el parametro venga nulo, se utiliza el corriente mes.
     *
     * @param null $period
     * @return boolean
     */
    public function isInvoiced($period=null)
    {
        if (!$period){
            $period = new DateTime('now');
        } else {
            if (!$period instanceof DateTime) {
                $period = new DateTime($period);
            }
        }
        return ($this->getProductToInvoices()
                ->andFilterWhere([
                    'date_format(period, \'%Y%m\')'=>$period->format('Ym'),
                    'status' => 'consumed'
                ])
                ->count() > 0);
    }

    /**
     * Verifica si se ha facturado el periodo pasado como parametro.
     * En caso de que el parametro venga nulo, se utiliza el corriente mes.
     *
     * @param null $period
     * @return boolean
     */
    public function canAddProductToInvoice($period=null)
    {
        if (!$period){
            $period = new DateTime('now');
        } else {
            if (!($period instanceof DateTime)) {
                $period = new DateTime($period);
            }
        }

        $query = $this->getProductToInvoices()
                ->andFilterWhere([
                    'date_format(period, \'%Y%m\')'=>$period->format('Ym'),
                    'status' => ['consumed', 'active']
                ]);

        return ($query->count() == 0);
    }

    /**
     * @return ActiveQuery
     */
    public function getVendor()
    {
        //No modificar esta linea, es correcto de esta forma:
        return $this->hasOne(Vendor::className(), ['vendor_id' => 'vendor_id']);
    }

    /**
     * Devuelve los contractDeatils asociados al vendedor, que aun no estan asociados una liquidación
     * @param int $vendor_id
     * @return array
     */
    public static function getForLiquidationSelect($vendor_id)
    {
        Yii::setLogger(new EmptyLogger());
        
        $query = (new Query())
            ->select(['cd.contract_detail_id', 'CONCAT(cus.lastname,", ",cus.name," | ", cus.code, " | ", p.name, " | ") as description']) //concat of the name shown in filter
            ->from('contract_detail cd')
            ->innerJoin('contract c', 'c.contract_id=cd.contract_id')
            ->innerJoin('customer cus', 'cus.customer_id=c.customer_id')
            ->innerJoin('product p', 'p.product_id=cd.product_id')
            ->leftJoin('vendor_liquidation_item vli', 'vli.contract_detail_id=cd.contract_detail_id')
            ->andWhere(['IS', 'vli.vendor_liquidation_item_id', NULL])
            ->andWhere(['cd.vendor_id' => $vendor_id]);

        $result = $query->all();

        //El precio final se calcula por programación para no complicar demasiado la query y ademas se aplican las reglas de negocios ya definidas
        $items = ArrayHelper::map($result, 'contract_detail_id',function($item){
           $detail = ContractDetail::findOne($item['contract_detail_id']);
           return $item['description']. Yii::$app->formatter->asCurrency($detail->product->finalPrice);
        });


        
        return $items;
    }
}
