<?php

namespace app\modules\westnet\models;

use app\components\db\ActiveRecord;
use app\modules\cobrodigital\models\PaymentCard;
use app\modules\sale\components\CodeGenerator\CodeGeneratorFactory;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "empty_ads".
 *
 * @property integer $empty_ads_id
 * @property integer $code
 * @property string $payment_code
 * @property integer $node_id
 * @property integer $used
 * @property integer $company_id
 * @property integer $payment_card_id
 * @property Company $company
 */
class EmptyAds extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'empty_ads';
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
    public function rules()
    {
        return [
            [['code', 'payment_code', 'node_id'], 'required'],
            [['code', 'node_id', 'used', 'company_id'], 'integer'],
            [['payment_code'], 'string', 'max' => 20],
            [['code'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'empty_ads_id' => 'Empty Ads ID',
            'code' => Yii::t('app','Code'),
            'payment_code' => Yii::t('app','Payment Code'),
            'node_id' => Yii::t('westnet','Node'),
            'used' => 'Used',
            'payment_card' => Yii::t('cobrodigital', 'PaymentCard'),
        ];
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
    public function getPaymentCard()
    {
        return $this->hasOne(PaymentCard::class, ['payment_card_id' => 'payment_card_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
     */
    protected function unlinkWeakRelations(){
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
    
    public static function maxCode()
    {
        $maxCode= (int)(new Query())
                    ->from('empty_ads')
                    ->max('code');
        
        return $maxCode;
    }

    /**
     * @param Company $company
     * @param $qty
     * @return bool
     * Verifica si la empresa está en condiciones de generar nuevos ADS vacíos.
     * En caso de tener un medio de pago que use tarjetas de cobro, se limitará a verificar si las tarjetas de cobro son suficientes para la cantidad de ADS que se desean crear.
     */
    public static function canCreateEmptyAds(Company $parent_company, $qty)
    {
        //La empresa desde la que se debe partir debe ser una padre.
        if ($parent_company->parent_id != null) {
            return false;
        }

        //Verifico si alguna de las empresas hijas tiene las tarjetas de cobro habilitadas
        if ($parent_company->hasEnabledTrackWithPaymentCards(true)) {

            $qty_percentage = round(($parent_company->getTotalADSPercentage() * $qty) / 100);

            //Verifico que la cantidad de ADS disponible sea mayor o igual a la cantidad porcentual entre las empresas hijas
            $availablePaymentCardsQty = PaymentCard::getUnusedPaymentCardsQty();
            if ($availablePaymentCardsQty < $qty_percentage) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Company $parent_company
     * @param $node
     * @param $qty
     * @return array
     * Crea ADS vacios.
     * Se tiene en cuenta el porcentaje de ADS que se deben crear para cada empresa hija.
     */
    public static function createEmptyAds(Company $parent_company, Node $node, $qty)
    {
        $codes = [];
        $associate_payment_card = false;

        foreach ($parent_company->companies as $company) {
            $generator = CodeGeneratorFactory::getInstance()->getGenerator('PagoFacilCodeGenerator');
            $percentage_qty = AdsPercentagePerCompany::getCompanyPercentageQty($company->company_id, $qty);
            if($company->hasEnabledTrackWithPaymentCards()) {
                $associate_payment_card = true;
            }

            for ($i = 0; $i < $percentage_qty; $i++) {
                $init_value = Customer::getNewCode();
                $code = str_pad($company->code, 4, "0", STR_PAD_LEFT) . ($company->code == '9999' ? '' : '000' ) .
                    str_pad($init_value, 5, "0", STR_PAD_LEFT) ;

                $payment_code = $generator->generate($code);

                $emptyAds = new EmptyAds([
                    'code' => $init_value,
                    'payment_code' => $payment_code,
                    'node_id' => $node->node_id,
                    'company_id' => $company->company_id,
                    'used' => false,
                ]);
                $emptyAds->save(false);


                if($associate_payment_card) {
                    $payment_card = $emptyAds->associatePaymentCard();
                    $codes[] = ['payment_code'=> $payment_code, 'code' => $init_value, '', 'barcode_url' => $payment_card->url];
                } else {
                    $codes[] = ['payment_code'=> $payment_code, 'code' => $init_value, ''];
                }
            }
        }

        return $codes;
    }

    /**
     * @return bool|mixed
     * Asocia una tarjeta de cobro que no ha sido usada a un ADS vacio.
     */
    public function associatePaymentCard()
    {
        $payment_card = PaymentCard::find()->where(['used' => 0])->one();

        if(!$payment_card) {
            return false;
        }

        $this->updateAttributes(['payment_card_id' => $payment_card->payment_card_id]);
        $payment_card->updateAttributes(['used' => 1]);

        return $payment_card;
    }
}
