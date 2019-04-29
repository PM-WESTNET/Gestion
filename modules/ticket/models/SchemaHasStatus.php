<?php

namespace app\modules\ticket\models;

use Yii;

/**
 * This is the model class for table "ticket.schema_has_status".
 *
 * @property int $schema_has_status_id
 * @property int $schema_id
 * @property int $status_id
 */
class SchemaHasStatus extends \app\components\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schema_has_status';
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
            [['schema_id', 'status_id'], 'required'],
            [['schema_id', 'status_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'schema_has_status_id' => Yii::t('app', 'Schema Has Status ID'),
            'schema_id' => Yii::t('app', 'Schema ID'),
            'status_id' => Yii::t('app', 'Status ID'),
        ];
    }
}
