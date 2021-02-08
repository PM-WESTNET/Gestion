<?php

namespace app\modules\agenda\models;

use Yii;

/**
 * This is the model class for table "user_group".
 *
 * @property integer $group_id
 * @property string $name
 * @property string $description
 *
 * @property UserGroupHasUser[] $userGroupHasUsers
 */
class UserGroup extends \app\components\db\ActiveRecord
{
    
    public $userModelClass;
    public $userModelId;
    
    private $_users;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        if (isset(Yii::$app->modules['agenda']->params['user']['class']))
            $this->userModelClass = Yii::$app->modules['agenda']->params['user']['class'];
        else
            $this->userModelClass = 'User';
        if (isset(Yii::$app->modules['agenda']->params['user']['idAttribute']))
            $this->userModelId = Yii::$app->modules['agenda']->params['user']['idAttribute'];
        else
            $this->userModelId = 'id';
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_group';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbagenda');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        $userModel = $this->userModelClass;
        $userPK = $this->userModelId;
        return $this->hasMany($userModel::className(), [$userPK => 'user_id'])->viaTable('user_group_has_user', ['group_id' => 'group_id']);
    }
    
    
    /**
     * @param array $users
     */
    public function setUsers($users) {
        
        if (empty($users) || !is_array($users)) {
            $users = [];
        } else {
            $users = array_filter($users);
        }
        
        $this->_users = $users;

        $saveUsers = function($event) {

            $userModel = $this->userModelClass;
            $userPK = $this->userModelId;
            
            $this->unlinkAll('users', true);
            
            foreach ($this->_users as $id) {
                $this->link('users', $userModel::findOne($id));
            }
            
        };

        $this->on(self::EVENT_AFTER_INSERT, $saveUsers);
        $this->on(self::EVENT_AFTER_UPDATE, $saveUsers);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'group_id' => \app\modules\agenda\AgendaModule::t('app', 'Group ID'),
            'name' => \app\modules\agenda\AgendaModule::t('app', 'Name'),
            'description' => \app\modules\agenda\AgendaModule::t('app', 'Description'),
            'userGroupHasUsers' => \app\modules\agenda\AgendaModule::t('app', 'UserGroupHasUsers'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGroupHasUsers()
    {
        return $this->hasMany(UserGroupHasUser::className(), ['group_id' => 'group_id']);
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
     * Weak relations: UserGroupHasUsers.
     */
    protected function unlinkWeakRelations(){
        $this->unlinkAll('users', true);
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

}
