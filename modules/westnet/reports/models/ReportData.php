<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 5/04/18
 * Time: 13:21
 */

namespace app\modules\westnet\reports\models;

use Yii;

/**
 * This is the model class for table "report_data".
 *
 * @property integer $report_data_id
 * @property string $report
 * @property integer $period
 * @property double $value
 */
class ReportData extends \app\components\db\ActiveRecord
{
    const REPORT_ACTIVE_CONNECTION  = 'active_connection';
    const REPORT_COMPANY_PASSIVE    = 'company_passive';
    const REPORT_DEBT_BILLS_1       = 'debt_bills_1';
    const REPORT_DEBT_BILLS_2       = 'debt_bills_2';
    const REPORT_DEBT_BILLS_3       = 'debt_bills_3';
    const REPORT_DEBT_BILLS_4       = 'debt_bills_4';
    const REPORT_UP                 = 'up';
    const REPORT_DOWN               = 'down';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_data';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['period', 'value'], 'required'],
            [['period'], 'integer'],
            [['value'], 'double'],
            [['report'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'report_data_id' => Yii::t('app', 'Report Data ID'),
            'report' => Yii::t('app', 'Report'),
            'period' => Yii::t('app', 'Period'),
            'value' => Yii::t('app', 'Value'),
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

}