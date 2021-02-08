<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 8/06/18
 * Time: 15:44
 */

namespace app\modules\pagomiscuentas\models;

use app\modules\checkout\models\Payment;
use app\modules\sale\models\Bill;
use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "partner".
 *
 * @property integer $pagomiscuentas_file_has_payment_id
 * @property integer $pagomiscuentas_file_id
 * @property integer $payment_id
 *
 * @property PagomiscuentasFile $pagomiscuentasFile
 * @property Payment $payment
 */
class PagomiscuentasFileHasPayment extends \app\components\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pagomiscuentas_file_has_payment';
    }

    public function rules(){

        return [
            [['payment_id', 'pagomiscuentas_file_id'],'required'],
        ];

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['payment_id' => 'payment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagomiscuentaFile()
    {
        return $this->hasOne(PagomiscuentasFile::className(), ['pagomiscuentas_file_id' => 'pagomiscuentas_file_id']);
    }
}