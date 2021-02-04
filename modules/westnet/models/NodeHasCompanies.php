<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 07/09/18
 * Time: 11:02
 */

namespace app\modules\westnet\models;

use app\modules\sale\models\Company;
use Yii;

/**
 * This is the model class for table "node".
 *
 * @property integer $node_has_companies_id
 * @property integer $company_id
 * @property integer $first_company_id
 * @property integer $second_company_id
 * @property integer $third_company_id
 * @property integer $node_id
 *
 * @property Node $node
 * @property Company $company
 * @property Company $firstCompany
 * @property Company $secondCompany
 * @property Company $thirdCompany
 *
 */
class NodeHasCompanies extends \app\components\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'node_has_companies';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {

        return [
            [['company_id', 'first_company_id', 'second_company_id','third_company_id', 'node_id'], 'integer'],
            [['node', 'company', 'firstCompany', 'secondCompany', 'thirdCompany'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'node_id' => Yii::t('westnet', 'Id'),
            'company_id' => Yii::t('westnet', 'Company'),
            'first_company_id' => Yii::t('westnet', 'First Company to Bill'),
            'first_company' => Yii::t('westnet', 'First Company to Bill'),
            'second_company_id' => Yii::t('westnet', 'Second Company to Bill'),
            'second_company' => Yii::t('westnet', 'Second Company to Bill'),
            'third_company_id' => Yii::t('westnet', 'Third Company to Bill'),
            'third_company' => Yii::t('westnet', 'Third Company to Bill'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNode()
    {
        return $this->hasOne(Node::className(), ['node_id' => 'node_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'first_company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecondCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'second_company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getThirdCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'third_company_id']);
    }
}