<?php

namespace app\modules\invoice\models;

use Yii;

/**
 * This is the model class for table "point_of_sale".
 *
 * @property integer $point_of_sale_id
 * @property integer $number
 * @property string $type
 * @property integer $blocked
 * @property string $dateto
 */
class PointOfSale extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'point_of_sale';
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
            [['number'], 'required'],
            [['number', 'blocked'], 'integer'],
            [['dateto'], 'safe'],
            [['type'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'point_of_sale_id' => 'Point Of Sale ID',
            'number' => 'Number',
            'type' => 'Type',
            'blocked' => 'Blocked',
            'dateto' => 'Dateto',
        ];
    }
}
