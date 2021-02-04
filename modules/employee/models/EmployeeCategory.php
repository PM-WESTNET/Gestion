<?php

namespace app\modules\employee\models;

use app\components\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "employee_category".
 *
 * @property int $employee_category_id
 * @property string $name
 * @property string $status
 *
 * @property Employee[] $employees
 */
class EmployeeCategory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['status'], 'string'],
            [['name'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'employee_category_id' => Yii::t('app', 'Employee Category ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployees()
    {
        return $this->hasMany(Employee::className(), ['employee_category_id' => 'employee_category_id']);
    }

    public function getStatusLabel() {
        $labels = [
            'enabled' => Yii::t('app','Enabled'),
            'disabled' => Yii::t('app','Disabled'),
        ];

        return $labels[$this->status];
    }

    public function getDeletable()
    {
        return !Employee::find()->andWhere(['employee_category_id' => $this->employee_category_id])->exists();
    }
}
