<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 08/10/18
 * Time: 11:28
 */

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "company_has_billing".
 *
 * @property integer $company_has_billing_id
 * @property integer $parent_company_id
 * @property integer $company_id
 * @property integer $bill_type_id
 *
 * @property BillType $billType
 * @property Company $parentCompany
 * @property Company $company
 */
class CompanyHasBilling extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_has_billing';
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
            [['parent_company_id', 'company_id', 'bill_type_id'], 'required'],
            [['parent_company_id', 'company_id', 'bill_type_id'], 'integer'],
            [['billType', 'parentCompany', 'company'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'company_has_billing_id' => Yii::t('app', 'Company Has Billing ID'),
            'parent_company_id' => Yii::t('app', 'Parent Company'),
            'company_id' => Yii::t('app', 'Company'),
            'bill_type_id' => Yii::t('app', 'Bill Type'),
            'billType' => Yii::t('app', 'BillType'),
            'parentCompany' => Yii::t('app', 'ParentCompany'),
            'company' => Yii::t('app', 'Company'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillType()
    {
        return $this->hasOne(BillType::className(), ['bill_type_id' => 'bill_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'parent_company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'company_id']);
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
     * Weak relations: BillType, ParentCompany, Company.
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

}
