<?php

use app\modules\provider\models\Provider;
use app\modules\sale\models\BillType;
use app\modules\sale\models\Company;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use app\components\companies\CompanySelector;

?>
<div class="provider-bill-filters">

    <?php $form = ActiveForm::begin(['method' => 'GET']); ?>
    <div class="row">
        <div class="col-sm-12">
            <?= CompanySelector::widget([
                'attribute'=>'company_id',
                'model' => $model,
                'selection' => $model->company_id ? $model->company_id : '',
                'inputOptions' => [
                    'prompt' => Yii::t('app', 'All'),
                    'placeholder' => 'Buscar Empresa'
                ]
            ]);?>
        </div>
    </div>
    <div>
        <div class="col-sm-6">
            <?= $form->field($model, 'last_activity_from')->widget(DatePicker::class, [
                'dateFormat' => 'dd-MM-yyyy',
                'options' => ['class' => 'form-control' ],
            ]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'last_activity_to')->widget(DatePicker::class, [
                'dateFormat' => 'dd-MM-yyyy',
                'options' => ['class' => 'form-control']
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-1 ">
            <?=  Html::submitButton('Filtrar', ['class'=> 'btn btn-primary'])?>
        </div>
    </div>

    <?php ActiveForm::end();?>
</div>