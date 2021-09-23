<?php

namespace app\modules\mobileapp\v1\models;
use Yii;

/**
 * This is the model class for table "statistics_app".
 *
 * @property integer $statistic_app_id
 * @property string $type
 * @property string $description
 * @property date $created_at
 */
class StatisticApp extends \app\components\db\ActiveRecord
{

    const TYPE_LOGIN = 'Login';
    const TYPE_ERROR_LOGIN = 'Error Login';
    const TYPE_COMPROBANTS = 'Visualización de comprobantes';
    const TYPE_CREATE_PAYMENT_EXTENSION = "Creación de Extensión de Pago";
    const TYPE_UPDATE_CUSTOMER = "Actualización de Clientes";
    const TYPE_ERROR_CODE = "Error";
    const TYPE_SEND_VALIDATION_CODE = "Envio de codigo de validación";

    public $total;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'statistic_app';
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
            [['created_at', 'description', 'type', 'total'], 'safe'],
            [['statistic_app_id'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'statistic_app_id' => 'Estadistica App ID',
            'type' => 'Tipo',
            'description' => 'Description',
            'created_at' => 'Fecha de Creación',
            'total' => 'Total'
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

    /**
    * Return statistic_app
    */
    public static function findModel($statistic_app_id){
        return self::find()->where(['statistic_app_id' => $statistic_app_id])->one();
    }

}
