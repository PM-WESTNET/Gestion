<?php

namespace app\modules\westnet\notifications\models;
use app\modules\sale\models\Customer;
use app\modules\sale\models\Company;
use Yii;

/**
 * This is the model class for table "siro_payment_intention".
 *
 * @property integer $siro_payment_intention_id
 * @property string $customer_id
 * @property string $hash
 * @property string $reference
 * @property string $url
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $status
 * @property string id_resultado
 */
class SiroPaymentIntention extends \app\components\db\ActiveRecord
{
    //Statuses 
    //*the problem with this is that in the db model is not setted as an Enum type. its just strings.. so yeah,. have it in mind
    const STATUS_PROCESSED = "PROCESADA";
    const STATUS_CANCELLED = "CANCELADA";
    const STATUS_ERROR = "ERROR";
    const STATUS_GENERATED = "GENERADA";
    const STATUS_REGISTERED = "REGISTRADA";
    const STATUS_NULL = null;


    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'siro_payment_intention';
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
            [['createdAt', 'updatedAt', 'status'], 'safe'],
            [['hash','id_resultado'], 'string', 'max' => 100],
            [['reference'], 'string', 'max' => 50],
            [['url'], 'string', 'max' => 70],
            [['siro_payment_intention_id','customer_id'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'siro_payment_intention_id' => 'Siro Payment Intention ID',
            'customer_id' => 'Cliente ID',
            'hash' => 'Hash',
            'reference' => 'Reference',
            'url' => 'Url',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
            'status' => 'Status',
            'id_resultado' => 'Resultado ID'
        ];
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

    public function getCustomer(){
        return $this->hasOne(Customer::class,['customer_id' => 'customer_id']);
    }

    public function getCompany(){
        return $this->hasOne(Company::class,['company_id' => 'company_id']);
    }

    /**
    * Return payment intention
    */
    public static function FindPaymentIntentionByID($id){
        return self::findOne(['siro_payment_intention_id' => $id]);
    }

    /**
     * Added this method to save all previous payment states in case of an error.
     */
    /* public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if(!$insert){
            $previous_state = $changedAttributes['status'];
            $this->previous_state = $previous_state;
            $this->updateAttributes(['previous_state']);
        }

    } */
}
