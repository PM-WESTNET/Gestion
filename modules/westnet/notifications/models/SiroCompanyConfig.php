<?php

namespace app\modules\westnet\notifications\models;

use Yii;
use app\modules\sale\models\Company;
/**
 * This is the model class for table "gestion_westnet.siro_company_config".
 *
 * @property int $id
 * @property int $company_id
 * @property int $is_enabled
 * @property string $company_agreement_id
 * @property string $created_at
 * @property string $updated_at
 */
class SiroCompanyConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siro_company_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'is_enabled'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['company_agreement_id'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'company_id']],
        ];
    }
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at','updated_at'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function(){
                    return date('Y-m-d H:i:s');
                }
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'is_enabled' => 'Is Enabled',
            'company_agreement_id' => 'Company Agreement ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getCompany()
    {
        return $this->hasOne(Company::class, ['company_id' => 'company_id']);
    }

    /**
     * gets all enabled companies from current model.
     * this is done to get better control over which 
     * companies have access to siro actions.
     * 
     * @return Array|false returns false if no enabled company is found
     */
    public static function getEnabledCompanies(){
        $companies_arr = false;
        $siro_enabled_companies = SiroCompanyConfig::findAll(['is_enabled'=>true]);
        if(!empty($siro_enabled_companies)){
            $companies_arr = [];
            foreach($siro_enabled_companies as $x_company_config){
                $companies_arr[$x_company_config['company_id']] = $x_company_config->company->name;
            }
            //format that the array should have when returning
            // var_dump(['2' => 'Redes del Oeste', '7' => 'Servicargas']);
        }

        //returns false if no company is found
        return $companies_arr;
    }

    public static function getEnabledCompaniesIds(){
        $siro_enabled_companies = SiroCompanyConfig::findAll(['is_enabled'=>true]);
        if(!empty($siro_enabled_companies)){
            $companies_arr = [];
            foreach($siro_enabled_companies as $x_company_config){
                $companies_arr[] = $x_company_config['company_id'];
            }
            return $companies_arr;
        }
        return false;
    }
}
