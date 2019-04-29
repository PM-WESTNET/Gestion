<?php

namespace app\modules\ticket\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ticket.schema".
 *
 * @property int $schema_id
 * @property string $name
 * @property string $class
 */
class Schema extends \app\components\db\ActiveRecord
{
    private $_statuses;

    public static function tableName()
    {
        return 'schema';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbticket');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'class'], 'required'],
            [['name', 'class'], 'string', 'max' => 255],
            [['statuses'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'schema_id' => Yii::t('app', 'Schema'),
            'name' => Yii::t('app', 'Name'),
            'class' => Yii::t('app', 'Class'),
            'statuses' => Yii::t('app', 'Statuses'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchemaHasStatus()
    {
        return $this->hasOne(ProductHasCategory::className(), ['product_id' => 'product_id']);
    }

    /**
     * Devuelve los estados del schema
     */
    public function getStatuses()
    {
        return $this->hasMany(Status::class, ['status_id' => 'status_id'])->viaTable('schema_has_status', ['schema_id' => 'schema_id']);
    }

    public function setStatuses($statuses){
        if(empty($statuses)){
            $statuses = [];
        }

        $this->_statuses = $statuses;

        $saveStatuses = function($event){
            //Quitamos las relaciones actuales
            $this->unlinkAll('statuses', true);

            //Guardamos las nuevas relaciones
            foreach ($this->_statuses as $id){
                $this->link('statuses', Status::findOne($id));
            }
        };

        $this->on(self::EVENT_AFTER_INSERT, $saveStatuses);
        $this->on(self::EVENT_AFTER_UPDATE, $saveStatuses);
    }

    /**
     * Permite determina si el esquema puede ser eliminado.
     * @return boolean
     */
    public function getDeletable(){

        if($this->getStatuses()->exists()){
            return false;
        }

        return true;
    }

    /**
     * @return array
     * Devuelve todos los schemas para ser listados en un desplegable
     */
    public static function getForSelect()
    {
        return ArrayHelper::map(self::find()->all(), 'schema_id', 'name');
    }

    /**
     * @return array
     * Devuelve todos los status para ser listados en un desplegable
     */
    public static function getStatusesForSelect()
    {
        return ArrayHelper::map(Status::find()->all(), 'status_id', 'name');
    }

    /**
     * @return array
     * Devuelve un listados de los estados que incluye el esquema de estados
     */
    public function getStatusesBySchema()
    {
        $all = [];
        foreach ($this->statuses as $status) {
            $all [] = [ 'id' => $status->status_id, 'name' => $status->name];
        }
        return $all;
    }
}
