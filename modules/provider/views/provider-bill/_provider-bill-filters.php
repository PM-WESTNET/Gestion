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

    <?php
    if (empty($model->start_date)) {
        $model->start_date=(new \DateTime('now -1 month'))->format('d-m-Y');
    }

    $form = ActiveForm::begin(['method' => 'GET']);
    ?>
    <div class="row">
        <div class="col-sm-6">
            <?=
            $form->field($model, 'start_date')->widget(DatePicker::className(), [
                'dateFormat' => 'dd-MM-yyyy',
                'clientOptions' => [
                    'yearRange' => '-46:+100',
                    'changeYear' => true],
                'options' => ['class' => 'form-control' ],

            ])
            ?>
        </div>

        <div class="col-sm-6">
            <?=
            $form->field($model, 'finish_date')->widget(DatePicker::className(), [
                'dateFormat' => 'dd-MM-yyyy',
                'clientOptions' => [
                    'yearRange' => '-46:+100',
                    'changeYear' => true],
                'options' => ['class' => 'form-control']
            ])
            ?>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= CompanySelector::widget(['attribute'=>'company_id', 'model' => $model, 'inputOptions' => ['prompt' => Yii::t('app', 'All'), 'placeholder' => 'Buscar Empresa']]);?>
        </div>

        <div class="col-sm-6">
            <?=
            $form->field($model, 'provider_id')->widget(Select2::className(), [
                'data' => ArrayHelper::map(Provider::find()->all(), 'provider_id', 'name'),
                'options' => ['placeholder' => 'Buscar Proveedor', 'encode' => false],
                'pluginOptions' => [
                    'allowClear' => true
                ]])
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?=
            $form->field($model, 'bill_type_id')->dropDownList(ArrayHelper::map(BillType::find()->all(), 'bill_type_id', 'name'),['prompt'=>'Seleccione un tipo de comprobante'])
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-1 ">
            <?=  Html::submitInput('Filtrar', ['class'=> 'btn btn-primary'])?>
        </div>
        <div class="col-sm-1">
            <?= Html::a('Borrar Filtros', Url::to(['provider-bill/index']), ['class' => 'btn btn-default'])?>
        </div>
    </div>

    <?php ActiveForm::end();?>


</div>