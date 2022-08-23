<?php

namespace app\modules\westnet\notifications\models;
use app\modules\sale\models\Company;

use Yii;

/**
 * This is the model class for table "gestion_notifications.company_has_notification_layout".
 *
 * @property int $id
 * @property string $layout_path
 * @property int $company_id
 * @property int $is_enabled
 */
class CompanyHasNotificationLayout extends \yii\db\ActiveRecord
{

    //todo: change to getAlias !!
    public $layouts_base_path = '@app/modules/westnet/notifications/body/layouts';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company_has_notification_layout';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbnotifications');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['layout_path', 'company_id'], 'required'],
            [['company_id', 'is_enabled'], 'integer'],
            [['layout_path'], 'string', 'max' => 255],
            [['is_enabled'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'layout_path' => 'Layout Path',
            'company_id' => 'Company ID',
            'is_enabled' => 'Is Enabled',
        ];
    }

    public function getCompany()
    {
        return $this->hasOne(Company::class, ['company_id' => 'company_id']);
    }

    /**
     * gets all layouts in an ordered array.
     * in case company_id is supplied, then only its layouts from the model are returned.
     * @return Array
     */
    public static function getLayouts($company_id = null){
        // define empty layouts
        $layouts = array();
        $layoutsQuery = self::find()->select(['layout_path'])->where(['company_id' => $company_id])->column();
        foreach($layoutsQuery as $layoutName){
            $layouts[$layoutName] = $layoutName;
        }
                
        /**
         * example of the format returned:
         * array (size=8)
         *   'Billing BW' => string 'Billing BW' (length=10)
         *   'Billing NEW' => string 'Billing NEW' (length=11)
         *   ...
         */
        return $layouts;
    }

}
