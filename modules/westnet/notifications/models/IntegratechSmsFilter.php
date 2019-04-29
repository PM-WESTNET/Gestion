<?php

namespace app\modules\westnet\notifications\models;

use app\components\helpers\DbHelper;
use app\modules\ticket\models\Category;
use Yii;

/**
 * This is the model class for table "arya_westnet_notifications.integratech_sms_filter".
 *
 * @property integer $integratech_sms_filter_id
 * @property string $word
 * @property string $action
 * @property string $category_id
 */
class IntegratechSmsFilter extends \app\components\db\ActiveRecord
{

    CONST ACTION_DELETE = 'delete';
    CONST ACTION_CREATE_TICKET = 'createTicket';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'integratech_sms_filter';
    }

    /**
     * @return Connection the database connection used by this AR class.
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDb() {
        return Yii::$app->get('dbnotifications');
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['word'],'unique'],
            [['word'], 'isOnlyOneWord'],
            [['action', 'status'], 'string'],
            [['category_id'], 'number'],
            ['state', 'required', 'when' => function ($model) {
                return $model->action == 'Create Ticket';
            }, 'whenClient' => "function (attribute, value) {
                return $('#action').val() == 'Create Ticket';
            }"],
            [['is_created_automaticaly'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'integratech_sms_filter_id' => \app\modules\westnet\notifications\NotificationsModule::t('app','Integratech Sms Filter ID'),
            'word' => \app\modules\westnet\notifications\NotificationsModule::t('app','Word'),
            'action' => \app\modules\westnet\notifications\NotificationsModule::t('app','Action'),
            'category_id' => \app\modules\westnet\notifications\NotificationsModule::t('app', 'Category'),
            'is_created_automaticaly' => \app\modules\westnet\notifications\NotificationsModule::t('app', 'Automaticaly created'),
        ];
    }

    public function isOnlyOneWord($attribute, $params, $validator)
    {
        $array_words = explode(' ', $this->$attribute);
        if(count($array_words) > 1){
            $this->addError($attribute, Yii::t('app','The value must be only a word'));
        }
    }

    public function getCategory(){
        return $this->hasOne(Category::className(),['category_id' => 'category_id']);
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

    public function wizard(){
        switch ($this->action){
            case IntegratechSmsFilter::ACTION_DELETE:
                return $this->delete();
                break;

            case IntegratechSmsFilter::ACTION_CREATE_TICKET:
                return $this->createTicket();
                break;
        }
    }

    public function delete(){}

    public function createTicket(){

    }

}
