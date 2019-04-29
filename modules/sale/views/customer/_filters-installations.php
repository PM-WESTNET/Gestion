<?php

use app\modules\westnet\models\Node;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
?>

<div class="instalations-filters">
    <?php $form = ActiveForm::begin(['method' => 'get', 'id' => 'filterForm']) ?>
        <div class="row">
            <div class="col-sm-4">
                <?= $form->field($model, 'customer_number')->textInput()->label(Yii::t('app', 'Customer Number')) ?>        
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'name')->textInput() ?>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'last_name')->textInput() ?>
            </div>

        </div>
        <div class="row">
            <div class="col-sm-3">
                <?= $form->field($model, 'from_date')->widget(DatePicker::className(), [
                        'language' => Yii::$app->language,
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control dates',
                            
                        ]
                ]) ?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'to_date')->widget(DatePicker::className(), [
                        'language' => Yii::$app->language,
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control dates',
                            
                        ]
                ]) ?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'vendor_id')->dropDownList(ArrayHelper::map(\app\modules\westnet\models\Vendor::find()->orderBy('lastname, name')->all(), 'vendor_id', 'fullName'), ['prompt' => Yii::t('app','Select an option...') ]) ?>
            </div>
            <div class="col-sm-3">
                <?= $this->render('_find-zone-with-autocomplete', ['form' => $form, 'model'=> $model]) ?>
            </div>
            
            
        </div>
        <div class="row">
            <div class="col-sm-2">
                <?= $form->field($model, 'min_tickets_count')->textInput()->label(Yii::t('app', 'Min Tickets Count'))?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'max_tickets_count')->textInput()->label(Yii::t('app', 'Max Tickets Count'))?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'min_bills_count')->textInput()->label(Yii::t('app', 'Min Bills Count'))?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'max_bills_count')->textInput()->label(Yii::t('app', 'Max Bills Count'))?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'min_debt')->textInput()->label(Yii::t('app', 'Min Debt'))?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'max_debt')->textInput()->label(Yii::t('app', 'Max Debt'))?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-1 ">
                <?=  Html::submitInput('Filtrar', ['class'=> 'btn btn-primary', 'id'=> 'filterButton'])?>
            </div>
            <div class="col-sm-1">
                <?= Html::a('Borrar Filtros', Url::to(['customer/installations']), ['class' => 'btn btn-default'])?>
            </div>
        </div>


<?php $form->end(); ?>     
</div>

