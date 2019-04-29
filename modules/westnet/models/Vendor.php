<?php

namespace app\modules\westnet\models;
use app\components\user\User;
use app\modules\config\models\Config;
use app\modules\provider\models\Provider;
use app\modules\sale\models\Company;
use app\modules\sale\models\DocumentType;
use app\modules\sale\models\Address;
use app\modules\accounting\models\Account;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\ContractDetail;
use yii\helpers\ArrayHelper;

use app\modules\ticket\TicketModule;
use app\modules\westnet\mesa\components\request\UsuarioRequest;
use Yii;

/**
 * This is the model class for table "vendor".
 *
 * @property integer $vendor_id
 * @property string $name
 * @property string $lastname
 * @property integer $document_type_id
 * @property string $document_number
 * @property string $sex
 * @property integer $address_id
 * @property integer $account_id
 * @property string $phone
 * @property integer $user_id
 * @property integer $vendor_commission_id
 * @property integer $external_user_id
 * @property integer $provider_id
 *
 * @property Account $account
 * @property Address $address
 * @property DocumentType $documentType
 * @property VendorCommission $commission
 * @property Provider $provider
 * @property $companies determina cuales van a ser las empresas padre (e hijas de las mismas) que el vendedor va a poder ver en el sistema en general
 */
class Vendor extends User
{
    public $updated_at;
    public static function tableName()
    {
        return 'vendor';
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
            [['document_type_id', 'address_id', 'account_id', 'user_id', 'vendor_commission_id', 'external_user_id', 'provider_id'], 'integer'],
            [['account', 'address', 'documentType', 'provider'], 'safe'],
            [['name', 'lastname', 'document_number', 'phone'], 'string', 'max' => 45],
            [['sex'], 'string', 'max' => 10],
            [['vendor_commission_id', 'external_user_id', 'provider_id', 'account_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'vendor_id' =>Yii::t('app','Vendor ID') ,
            'name' => Yii::t('app','Name'),
            'lastname' => Yii::t('app','Lastname'),
            'document_type_id' => Yii::t('app','Document Type'),
            'document_number' => Yii::t('app','Document Number'),
            'sex' => Yii::t('app','Sex'),
            'address_id' => Yii::t('app','Address'),
            'account_id' => Yii::t('app','Account'),
            'phone' => Yii::t('app','Phone'),
            'user_id' => Yii::t('app','User'),
            'account' => Yii::t('app','Account'),
            'address' => Yii::t('app','Address'),
            'documentType' => Yii::t('app','Document Type'),
            'external_user_id' => Yii::t('app', 'External User'),
            'provider_id' => Yii::t('app', 'Provider'),
            'companies' => Yii::t('app', 'Access to companies'),
            'username' => Yii::t('app', 'Username')
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['account_id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Address::className(), ['address_id' => 'address_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentType()
    {
        return $this->hasOne(DocumentType::className(), ['document_type_id' => 'document_type_id']);
    }
        
    public function getLiquidations()
    {
        return $this->hasMany(VendorLiquidation::className(), ['vendor_id' => 'vendor_id']);
    }
        
    public function getCommission()
    {
        return $this->hasOne(VendorCommission::className(), ['vendor_commission_id' => 'vendor_commission_id']);
    }

    /**
     * @return Usuario
     */
    public function getExternalUser()
    {
        $api = new UsuarioRequest(Config::getValue('mesa_server_address'));
        return $api->findById($this->external_user_id);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::className(), ['provider_id' => 'provider_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return !Contract::find()->andWhere(['vendor_id' => $this->vendor_id])->exists() &&
            !ContractDetail::find()->andWhere(['vendor_id' => $this->vendor_id]) &&
            !VendorLiquidation::find()->andWhere(['vendor_id' => $this->vendor_id]);
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Account, Address, DocumentType.
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

    public function getFullName()
    {
        return $this->lastname . ", " . $this->name;
    }
    
    /**
     * Busca el modelo Vendor asociado al user_id
     * @param int $user_id
     * @return Vendor
     */
    public static function findByUserId($user_id)
    {
        return self::find()->where(['user_id' => $user_id])->one();
    }
    
    /**
     * Busca el modelo Vendor asociado al user_id del usuario actual
     * @param int $user_id
     * @return Vendor
     */
    public static function findCurrentVendor()
    {
        return self::findByUserId(Yii::$app->user->id);
    }
    
    /**
     * Busca el modelo Vendor asociado al user_id
     * @param int $user_id
     * @return Vendor
     */
    public static function vendorExists($user_id)
    {
        return self::find()->where(['user_id' => $user_id])->exists();
    }
    
    public static function findForSelect()
    {
        $vendors = self::find()->all();
        
        return ArrayHelper::map($vendors, 'vendor_id', 'fullName');
    }

    public static function getExternalVendors(){
        return self::find()->where(['>','external_user_id', 0])->andWhere(['not', ['external_user_id' => null]])->all();
    }

    public function setAuth_key($authKey)
    {
        if($this->getUser()->exists()) {
            $this->getUser()->one()->auth_key = $authKey;
        }
    }

    public function getAuth_key()
    {
        if($this->getUser()->exists()) {
            return $this->getUser()->one()->auth_key;
        }
        return null;
    }

    public function setCreated_at($created_at)
    {
        if($this->getUser()->exists()) {
            $this->getUser()->one()->created_at = $created_at;
        }
    }

    public function getCreated_at()
    {
        if($this->getUser()->exists()) {
            return $this->getUser()->one()->created_at;
        }
        return null;
    }

    public function setRegistration_ip($registration_ip)
    {
        if($this->getUser()->exists()) {
            $this->getUser()->one()->registration_ip = $registration_ip;
        }
    }

    public function getRegistration_ip()
    {
        if($this->getUser()->exists()) {
            return $this->getUser()->one()->registration_ip;
        }
        return null;
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     * devuelve un listado de vendedores, teniendo en cuenta el estado de su usuario, ordenado por username
     */
    public static function getVendorsWithActiveUser()
    {
        return Vendor::find()
            ->leftJoin('user', 'user.id = vendor.user_id')
            ->andWhere(['OR',['IS', 'user.status', null], ['user.status' => 1]])
            ->orderBy(['lastname' => SORT_ASC, 'name' => SORT_ASC])
            ->all();
    }
}
