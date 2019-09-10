<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use webvimark\modules\UserManagement\models\User;
use yii\helpers\Url;
use app\modules\ticket\components\schemas\SchemaCobranza;
use yii\jui\DatePicker;

$form= ActiveForm::begin(['method' => 'GET']);
?>


<div class="ticket_filters">
    <div class="row">

        <div class="col-sm-3">
            <?= $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form' => $form, 'model' => $model, 'attribute' => 'customer_id']) ?>
        </div>

        <div class="col-sm-3">
            <?=$form->field($model, 'status_id')->dropDownList(ArrayHelper::map(SchemaCobranza::getSchemaStatuses(), 'status_id', 'name'), ['prompt' => Yii::t('app', 'All')])?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'assignations')->widget(Select2::class, [
                'data' => ArrayHelper::map(User::find()->where(['status' => 1])->all(), 'id', 'username'),
                'options' => ['placeholder' => Yii::t('app','Select')],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'created_by')->widget(Select2::class, [
                'data' => ArrayHelper::map(User::find()->where(['status' => 1])->all(), 'id', 'username'),
                'options' => ['placeholder' => Yii::t('app','Select')],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'start_date_from')->widget(DatePicker::class, [
                    'model' => $model,
                    'attribute' => 'start_date_from',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                            'class' => 'form-control'
                    ]
            ])?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'start_date_to')->widget(DatePicker::class, [
                'model' => $model,
                'attribute' => 'start_date_to',
                'dateFormat' => 'yyyy-MM-dd',
                'options' => [
                    'class' => 'form-control'
                ]
            ])?>
        </div>

        <div class="col-sm-4">
            <?= $form->field($model, 'title')->textInput() ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'ticket_management_qty')->textInput() ?>
        </div>

    </div>

    <div class="row">

        <div class="col-lg-1">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>

        </div>
        <dvi class="col-lg-1">
            <?= \yii\bootstrap\Html::a('Borrar Filtros', Url::to(['index']), ['class' =>'btn btn-default'])?>
        </dvi>
    </div>


</div>
<?php $form->end()?>
</div>
