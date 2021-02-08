<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 8/06/18
 * Time: 15:44
 */

namespace app\modules\pagomiscuentas\models;

use app\components\db\ActiveRecord;
use app\modules\sale\models\Bill;
use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "partner".
 *
 * @property integer $pagomiscuentas_file_has_bill_id
 * @property integer $pagomiscuentas_file_id
 * @property integer $bill_id
 *
 * @property PagomiscuentasFile $pagomiscuentasFile
 * @property Bill $bill
 */
class PagomiscuentasFileHasBill extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pagomiscuentas_file_has_bill';
    }

    public function rules(){

        return [
            [['bill_id', 'pagomiscuentas_file_id'],'required'],
        ];

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBill()
    {
        return $this->hasOne(Bill::className(), ['bill_id' => 'bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagomiscuentaFile()
    {
        return $this->hasOne(PagomiscuentasFile::className(), ['pagomiscuentas_file_id' => 'pagomiscuentas_file_id']);
    }
}