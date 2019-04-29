<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 31/05/18
 * Time: 10:27
 */

namespace app\components\helpers;


use Yii;
use yii\validators\Validator;

class CuitValidator extends Validator
{

    public function validateAttribute($model, $attribute)
    {
        // SI SI, HARDCODEADO!! Hay que cambiar los modelos para poder parametrizarlo, o meter config y eso...
        if( array_key_exists('document_type_id', $model->attributes ) !== false  ) {
            if($model->document_type_id != 1) {
                return false;
            }
        }
        if( array_key_exists('tax_condition_id', $model->attributes ) !== false  ) {
            if($model->tax_condition_id == 3) {
                return false;
            }
        }

        $value = $model->$attribute;
        $valid = false;
        $cuit = str_replace('_', '', str_replace('-', '', $value));
        $aMult = [5,4,3,2,7,6,5,4,3,2];

        if ($cuit && strlen($cuit)== 11) {
            $aCUIT = str_split($cuit);
            $iResult = 0;
            for($i = 0; $i <= 9; $i++)
            {
                $iResult += $aCUIT[$i] * $aMult[$i];
            }
            $iResult = ($iResult % 11);
            $iResult = 11 - $iResult;

            if ($iResult == 11) $iResult = 0;
            if ($iResult == 10) $iResult = 9;

            if ($iResult == $aCUIT[10])
            {
                $valid = true;
            }
        }
        if(!$valid) {
            $this->addError($model, $attribute, Yii::t('app', 'The CUIT is invalid.'));
        }
    }
}