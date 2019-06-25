<?php

namespace app\modules\instructive\models;

use webvimark\modules\UserManagement\models\rbacDB\Role;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "instructive_category".
 *
 * @property integer $instructive_category_id
 * @property string $name
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Instructive[] $instructives
 * @property InstructiveCategoryHasRole[] $instructiveCategoryHasRoles
 */
class InstructiveCategory extends \app\components\db\ActiveRecord
{

    const STATUS_ENABLED = 10;
    const STATUS_DISABLED = 31;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'instructive_category';
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
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            /**'date' => [
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
            ],**/
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 45],
            [['instructiveCategoryHasRoles'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instructive_category_id' => Yii::t('app', 'Instructive Category ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'instructives' => Yii::t('app', 'Instructives'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstructives()
    {
        return $this->hasMany(Instructive::className(), ['instructive_category_id' => 'instructive_category_id']);
    }
    
        
             
    /**
     * @inheritdoc
     * Strong relations: Instructives.
     */
    public function getDeletable()
    {
        if($this->getInstructives()->exists()){
            return false;
        }
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

    public function getStatusLabel()
    {
        $label = [
            self::STATUS_ENABLED => Yii::t('app','Enabled'),
            self::STATUS_DISABLED => Yii::t('app','Disabled'),
        ];

        return $label[$this->status];
    }

    public function getInstructiveCategoryHasRoles()
    {
        return $this->hasMany(InstructiveCategoryHasRole::class, ['instructive_category_id' => 'instructive_category_id']);
    }

    public function setInstructiveCategoryHasRoles($roles)
    {
        if ($this->instructiveCategoryHasRoles) {
            $this->unlinkAll('instructiveCategoryHasRoles', true);
        }

        $save = function () use ($roles) {
            foreach ($roles as $role) {
                $ichr= new InstructiveCategoryHasRole([
                    'role_code' => $role,
                    'instructive_category_id' => $this->instructive_category_id
                ]);

                $ichr->save();
            }
        };

        $this->on(ActiveRecord::EVENT_AFTER_INSERT, $save);
        $this->on(ActiveRecord::EVENT_AFTER_UPDATE, $save);
    }

    public function getRoles()
    {
        return $this->hasMany(Role::class, ['name' => 'role_code'])->viaTable('instructive_category_has_role', ['instructive_category_id' => 'instructive_category_id']);
    }

}
