<?php

namespace app\modules\ticket\models;

use Yii;
use app\modules\ticket\TicketModule;

/**
 * This is the model class for table "type".
 *
 * @property integer $type_id
 * @property integer $user_group_id
 * @property string $name
 * @property string $description
 * @property string $duration
 * @property string $slug
 *
 * @property Ticket[] $tickets
 * @property Category[] $categories
 * @property UserGroup $userGroup
 */
class Type extends \app\components\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'type';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbticket');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'slug', 'duration'], 'required'],
            [['user_group_id'], 'integer'],
            [['description'], 'string'],
            [['name', 'slug'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'type_id' => TicketModule::t('app', 'Type'),
            'user_group_id' => TicketModule::t('app', 'User Group'),
            'name' => TicketModule::t('app', 'Name'),
            'description' => TicketModule::t('app', 'Description'),
            'slug' => TicketModule::t('app', 'Slug'),
            'tickets' => TicketModule::t('app', 'Tickets'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGroup() {
        return $this->hasOne(\app\modules\agenda\models\UserGroup::className(), ['user_group_id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTickets() {
        return $this->hasMany(Ticket::className(), ['type_id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories() {
        return $this->hasMany(Category::className(), ['type_id' => 'type_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: Tickets.
     */
    public function getDeletable() {
        if ($this->getTickets()->exists()) {
            return false;
        }
        if ($this->getCategories()->exists()) {
            return false;
        }
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
     */
    protected function unlinkWeakRelations() {
        
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            if ($this->getDeletable()) {
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

}
