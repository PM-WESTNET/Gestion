<?php

use app\modules\ticket\models\Color;
use app\modules\ticket\models\Status;
use app\modules\ticket\models\Category;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;



$form= ActiveForm::begin(['method' => 'GET']);
?>


<div class="ticket_filters">
    <div class="row">
        <div class="col-lg-4">
            <?=$form->field($model, 'document')->textInput()?>
        </div>

        <div class="col-lg-4">
            <?=$form->field($model, 'customer_number')->textInput()?>
        </div>

        <div class="col-lg-4">
            <?=$form->field($model, 'customer')->textInput()?>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-4">
            <?=$form->field($model, 'color_id')->dropDownList(ArrayHelper::map(Color::find()->orderBy('order')->all(), 'color_id', 'name'), ['prompt' => Yii::t('app', 'All')])?>
        </div>

        <div class="col-lg-4">
            <?=$form->field($model, 'category_id')->dropDownList(ArrayHelper::map(Category::find()->orderBy('name')->all(), 'category_id', 'name'), ['prompt' => Yii::t('app', 'All')])?>
        </div>

        <div class="col-lg-4">
            <?=$form->field($model, 'status_id')->dropDownList(ArrayHelper::map(Status::find()->all(), 'status_id', 'name'), ['prompt' => Yii::t('app', 'All')])?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'title')->textInput()?>
        </div>

        <div class="col-lg-6">
            <?= $form->field($model, 'assignations')->textInput()?>
        </div>


    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'start_date')->widget(\kartik\daterange\DateRangePicker::className(), [
                'convertFormat' => true,
                'useWithAddon' => false,
                'model' => $model,
                'attribute' => 'start_date',
                'presetDropdown' => true,
                'hideInput' => true,
                'pluginOptions' => [
                    'locale' => [
                        'format' => 'd-m-Y',
                        'separator' => ' al ',
                    ],
                    'opens' => 'left'
                ]])?>
        </div>

        <div class="col-lg-6">
            <?=$form->field($model, 'finish_date')->widget(\kartik\daterange\DateRangePicker::className(), [
                'convertFormat' => true,
                'useWithAddon' => false,
                'model' => $model,
                'attribute' => 'finish_date',
                'presetDropdown' => true,
                'hideInput' => true,
                'pluginOptions' => [
                    'locale' => [
                        'format' => 'd-m-Y',
                        'separator' => ' al ',
                    ],
                    'opens' => 'left'
                ]])?>
        </div>

    </div>

    <div class="row">

        <div class="col-lg-1">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>

        </div>
        <dvi class="col-lg-1">
            <?= \yii\bootstrap\Html::a('Borrar Filtros', yii\helpers\Url::to(['index']), ['class' =>'btn btn-default'])?>
        </dvi>
    </div>


</div>
<?php $form->end()?>
</div>
