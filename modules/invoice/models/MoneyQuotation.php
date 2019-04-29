<?php

namespace app\modules\invoice\models;

use Yii;

/**
 * This is the model class for table "money_quotation".
 *
 * @property integer $money_quotation_id
 * @property string $code
 * @property double $price
 * @property string $date
 */
class MoneyQuotation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'money_quotation';
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
            [['code', 'price', 'date'], 'required'],
            [['price'], 'number'],
            [['date'], 'safe'],
            [['code'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'money_quotation_id' => 'Money Quotation ID',
            'code' => 'Code',
            'price' => 'Price',
            'date' => 'Date',
        ];
    }
}
