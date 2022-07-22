<?php

use app\modules\sale\models\CustomerPreviousCompany;
use yii\helpers\ArrayHelper;

$companies=CustomerPreviousCompany::find()->all();
            
$listData=ArrayHelper::map($companies,'id','company');

echo $form->field($model, 'previous_company_id')->dropDownList(
$listData,
['prompt'=>'Seleccione una empresa...','id'=>'prevCompany','onchange'=>'console.log("averteeeee")']
)->label(Yii::t('app', 'Customer Previous Company'));
