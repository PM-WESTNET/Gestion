<?php

namespace app\modules\invoice\models;

use Yii;

/**
 * This is the model class for table "generic_type".
 *
 * @property string $service
 * @property string $type
 * @property string $code
 * @property string $description
 * @property string $datefrom
 * @property string $dateto
 */
class GenericType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'generic_type';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbafip');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service', 'type', 'code', 'description'], 'required'],
            [['datefrom', 'dateto'], 'safe'],
            [['service'], 'string', 'max' => 10],
            [['service', 'type', 'code'], 'unique', 'targetAttribute' => ['service', 'type','code']],
            [['type'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'service' => 'Service',
            'type' => 'Type',
            'code' => 'Code',
            'description' => 'Description',
            'datefrom' => 'Datefrom',
            'dateto' => 'Dateto',
        ];
    }
}
