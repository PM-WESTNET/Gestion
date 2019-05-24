<?php

namespace app\modules\sale\models;

use app\modules\checkout\models\CompanyHasPaymentTrack;
use app\modules\checkout\models\Track;
use app\modules\partner\models\PartnerDistributionModel;
use app\modules\westnet\models\AdsPercentagePerCompany;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "company".
 *
 * @property integer $company_id
 * @property string $name
 * @property string $status
 * @property string $tax_identification
 * @property string $address
 * @property string $phone
 * @property string $email
 * @property integer $parent_id
 * @property string $certificate
 * @property string $key
 * @property string $create_timestamp
 * @property string $iibb
 * @property date $start
 * @property string $fantasy_name
 * @property string $certificate_phrase
 * @property string $code
 * @property integer $partner_distribution_model_id
 * @property string $web
 * @property string $portal_web
 * @property integer $pagomiscuentas_code
 *
 * @property Company $parent
 * @property Company[] $companies
 * @property CompanyHasBillType[] $companyHasBillTypes
 * @property BillType[] $billTypes
 * @property PartnerDistributionModel $partnerDistributionModel
 */
class Company extends \app\components\db\ActiveRecord
{

    private $_billTypes;

    private $_paymentTracks;

    private $_defaultBillType;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_timestamp'],
                ],
                'value' => function(){return time();}
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'status', 'tax_condition_id', 'tax_identification', 'code', 'partner_distribution_model_id'], 'required'],
            [['name', 'address', 'email', 'fantasy_name', 'certificate_phrase'], 'string', 'max' => 255],
            [['web', 'portal_web'], 'string', 'max' => 100],
            [['status'], 'in', 'range' => ['enabled', 'disabled']],
            [['parent_id', 'tax_condition_id', 'partner_distribution_model_id', 'pagomiscuentas_code'], 'integer'],
            [['billTypes', 'partnerDistributionModel', 'paymentTracks'], 'safe'],
            [['tax_identification', 'phone', 'iibb', 'technical_service_phone'], 'string', 'max' => 45],
            [['code'], 'string', 'max' => 4],
            [['certificate'], 'file', 'extensions' => 'crt'],
            [['key'], 'file', 'extensions' => 'key'],
            [['start'], 'date'],
            [['default'], 'boolean'],
            [['email'], 'email'],
            [['defaultBillType'], 'safe'],
            [['logo'], 'file', 'extensions' => 'png,jpg'],
            [['web', 'portal_web'], 'url'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'company_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'fantasy_name' => Yii::t('app', 'Fantasy Name'),
            'status' => Yii::t('app', 'Status'),
            'tax_identification' => Yii::t('app', 'Tax Identification'),
            'address' => Yii::t('app', 'Address'),
            'phone' => Yii::t('app', 'Phone'),
            'email' => Yii::t('app', 'Email'),
            'parent_id' => Yii::t('app', 'Parent'),
            'certificate' => Yii::t('app', 'Certificate'),
            'key' => Yii::t('app', 'Key'),
            'parent' => Yii::t('app', 'Parent'),
            'billTypes' => Yii::t('app', 'Bill Types'),
            'paymentTracks' => Yii::t('app', 'Payment methods and tracks'),
            'taxCondition' => Yii::t('app', 'Tax Condition'),
            'iibb' => Yii::t('app', 'IIBB'),
            'start' => Yii::t('app', 'Company Start Date'),
            'default' => Yii::t('app', 'Default'),
            'defaultBillType' => Yii::t('app', 'Default bill type'),
            'logo' => Yii::t('app', 'Logo'),
            'certificate_phrase' => Yii::t('app', 'Certificate Phrase'),
            'code' => Yii::t('app', 'Entity Payment Code'),
            'partnerDistributionModel' => Yii::t('partner', 'Partner Distribution Model'),
            'partner_distribution_model_id' => Yii::t('partner', 'Partner Distribution Model'),
            'web' => Yii::t('app', 'Web'),
            'portal_web' => Yii::t('app', 'Portal Web'),
            'pagomiscuentas_code' => Yii::t('app', 'Pago Mis Cuentas Code'),
            'technical_service_phone' => Yii::t('app', 'Technical service phone')
        ];
    }    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::className(), ['parent_id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyHasBillTypes()
    {
        return $this->hasMany(CompanyHasBillType::className(), ['company_id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillTypes()
    {
        return $this->hasMany(BillType::className(), ['bill_type_id' => 'bill_type_id'])->viaTable('company_has_bill_type', ['company_id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTracks()
    {
        return $this->hasMany(Track::class, ['track_id' => 'track_id'])->viaTable('company_has_payment_track', ['company_id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentTracks()
    {
        return $this->hasMany(CompanyHasPaymentTrack::class, ['company_id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentEnabledTracks()
    {
        return $this->hasMany(CompanyHasPaymentTrack::class, ['company_id' => 'company_id'])->where(['status' => 'enabled']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPointsOfSale()
    {
        return $this->hasMany(PointOfSale::className(), ['company_id' => 'company_id']);
    }
    
    /**
     * Devuelve el pto de venta por defecto
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultPointOfSale()
    {
        
        return $this->getPointsOfSale()->where(['default' => 1])->one();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxCondition()
    {
        return $this->hasOne(TaxCondition::className(), ['tax_condition_id' => 'tax_condition_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerDistributionModel()
    {
        return $this->hasOne(PartnerDistributionModel::className(), ['partner_distribution_model_id' => 'partner_distribution_model_id']);
    }
        
    /**
     * @brief Sets BillTypes relation on helper variable and handles events insert and update
     */
    public function setBillTypes($billTypes){

        if(empty($billTypes)){
            $billTypes = [];
        }

        $this->_billTypes = $billTypes;

        $this->on(self::EVENT_AFTER_VALIDATE, function($event){
            if(!in_array($this->_defaultBillType, $this->_billTypes)){
                $this->addError('defaultBillType', Yii::t('app', 'Default bill type not allowed.'));
            }
        });

        $saveBillTypes = function($event){
            $this->unlinkAll('billTypes', true);

            foreach ($this->_billTypes as $id) {
                if($id != $this->_defaultBillType){
                    $this->link('billTypes', BillType::findOne($id));
                }else{
                    $this->link('billTypes', BillType::findOne($id), ['default'=>1]);
                }
            }
        };
        $this->on(self::EVENT_AFTER_INSERT, $saveBillTypes);
        $this->on(self::EVENT_AFTER_UPDATE, $saveBillTypes);
    }

    public function setPaymentTracks($paymentTracks){

        if(empty($paymentTracks)){
            $paymentTracks = [];
        }

        $this->_paymentTracks = $paymentTracks;

        $savePaymentTracks = function($event){
            $this->unlinkAll('paymentTracks', true);

            foreach ($this->_paymentTracks['Track'] as $payment_method_id => $track_id) {
                $payment_method_status = array_key_exists('Payment_method', $this->_paymentTracks) ? (array_key_exists($payment_method_id, $this->_paymentTracks['Payment_method']) ? CompanyHasPaymentTrack::STATUS_ENABLED : CompanyHasPaymentTrack::STATUS_DISABLED) : CompanyHasPaymentTrack::STATUS_DISABLED;
                $company_has_payment_track = new CompanyHasPaymentTrack([
                    'company_id' => $this->company_id,
                    'payment_method_id' => $payment_method_id,
                    'status' => $payment_method_status,
                    'track_id' => $track_id,
                ]);
                $company_has_payment_track->save();
            }
        };
        $this->on(self::EVENT_AFTER_INSERT, $savePaymentTracks);
        $this->on(self::EVENT_AFTER_UPDATE, $savePaymentTracks);
    }

    /**
     * @inheritdoc
     * Strong relations: Parent, BillTypes.
     */
    public function getDeletable()
    {
        if($this->getCompanies()->exists()){
            return false;
        }
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: BillTypes.
     */
    protected function unlinkWeakRelations(){
        
        $this->unlinkAll('billTypes', true);
        $this->unlinkAll('pointsOfSale', true);

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
     * Solo un default (habilitado)
     * @param type $insert
     */
    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){

            if($this->default && $this->status == 'enabled'){
                Company::updateAll(['default' => 0], ['status' => 'enabled']);
            }
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

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
        $this->start = Yii::$app->formatter->asDate($this->start);
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
        $this->start = Yii::$app->formatter->asDate($this->start, 'yyyy-MM-dd');
    }

    public static function findDefault()
    {
        $company = Company::find()->where(['default' => 1, 'status' => 'enabled'])->one();
        if($company === null){
            throw new \yii\web\HttpException(500, 'Default company not defined.');
        }
        return $company;
    }

    /**
     * Devuelve una lista con los nombres de los tipos de factura
     * @return string
     */
    public function getBillTypesNames($separator = ', '){
        $names = null;
        foreach($this->billTypes as $i => $type){
            if(($i+1) == count($this->billTypes)){
                $names .= $type->name;
            }else{
                $names .= $type->name.$separator;
            }
        }

        return $names;
    }

    public function setDefaultBillType($value)
    {

        $this->_defaultBillType = $value;

    }

    public function getDefaultBillType()
    {
        if($this->getBillTypes()->viaTable('company_has_bill_type', ['company_id' => 'company_id'], function($query){ $query->where(['default'=>1]); })->exists()){
            return $this->getBillTypes()->viaTable('company_has_bill_type', ['company_id' => 'company_id'], function($query){ $query->where(['default'=>1]); })->one();
        }

        return $this->getBillTypes()->one();
    }

    public function checkBillType($billType){

        return $this->getBillTypes()->where(['bill_type.bill_type_id'=>$billType->bill_type_id])->exists();

    }

    public function getLogoWebPath()
    {
        return (!$this->logo ? null : 'uploads/logos/'.basename($this->logo) );
    }

    public function findForAutoComplete($text)
    {
        Yii::debug(Company::find()->where("ucase(name) LIKE :name")->params([':name'=>mb_strtolower($text)."%"])->all() );
        return ArrayHelper::map(self::find()->where("ucase(name) LIKE :name")->params([':name'=>mb_strtolower($text)."%"])->all(), 'company_id', 'name');
    }

    public static function getParentCompanies()
    {
        return Company::find()->where(['parent_id' => null])->andWhere(['status' => 'enabled'])->all();
    }

    /**
     * @param bool $verify_child_companies
     * @return bool
     *      Verifica si en alguno de sus medios de pago tiene asociado algun canal que utilice tarjetas de cobro
     * Es posible pasarle el parámetro verify_child_companies en true para que se fije si una de las empresas hijas lo
     * tiene habilitado, ademas de ella misma
     */
    public function hasEnabledTrackWithPaymentCards($verify_child_companies = false)
    {
        $has_track_with_payment_card = false;

        if($verify_child_companies) {
            foreach ($this->companies as $company) {
                if($company->getTracks()->where(['use_payment_card' => 1])->exists()) {
                    $has_track_with_payment_card = true;
                }
            }
        }

        if($this->getTracks()->where(['use_payment_card' => 1])->exists()) {
            $has_track_with_payment_card = true;
        }

        return $has_track_with_payment_card;
    }

    public function getCompaniesWithPaymentCards() {
        $companies_with_payment_card = [];

        foreach ($this->companies as $company) {
            if ($company->getTracks()->where(['use_payment_card' => 1])->exists()) {
                array_push($companies_with_payment_card, $company->company_id);
            }
        }

        return $companies_with_payment_card;
    }

    public function getTotalADSPercentage()
    {
        $total_percentage = 0;

        //Para el calculo solo tengo en cuenta las empresas hijas que tienen las tarjetas de cobro habilitadas
        $companies_with_payment_card = $this->getCompaniesWithPaymentCards();

        //Obtengo el porcentaje de ads que va tenerse en cuenta de las empresas hijas
        foreach ($companies_with_payment_card as $company_id) {
            $total_percentage += AdsPercentagePerCompany::getCompanyPercentage($company_id);
        }

        return $total_percentage;
    }
}
