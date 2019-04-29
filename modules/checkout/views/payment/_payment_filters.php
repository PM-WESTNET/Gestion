<?php

use app\components\companies\CompanySelector;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;


$form= ActiveForm::begin(['method' => 'GET']);
?>

<div class="_payment_filters">
    
    <div class="row">
        <div class="col-lg-4">
            <?=$form->field($search, 'customer_number')->textInput()?>
        </div>
        <div class="col-lg-4">
            <?=$form->field($search, 'customer_name')->textInput()?>
        </div>
        <div class="col-lg-4">
            <?=$form->field($search, 'customer_lastname')->textInput()?>
        </div>
        
    </div>
    <div class="row">
        <div class="col-lg-4">
            <?= CompanySelector::widget(['attribute'=>'company_id', 'model' => $search]);?>
        </div>
        <div class="col-lg-4">
             <?= $form->field($search, 'from_date')->widget(DatePicker::className(), [
                        'language' => Yii::$app->language,
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control dates',
                            
                        ]
                ]) ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($search, 'to_date')->widget(DatePicker::className(), [
                        'language' => Yii::$app->language,
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control dates',
                            
                        ]
                ]) ?>
        </div>
             
    </div>
    <div class="row">
        <div class="col-lg-4">
            <?=$form->field($search, 'from_amount')->textInput()?>
        </div>
        <div class="col-lg-4">
            <?=$form->field($search, 'to_amount')->textInput()?>
        </div>
        <div class="col-lg-4">
            <?=$form->field($search, '_status')->checkboxList([
                'draft' => Yii::t('accounting', 'Draft'), 
                'closed' => Yii::t('app', 'Closed'), 
                'conciled' => Yii::t('accounting', 'Conciled'), 
                'cancelled' => Yii::t('accounting', 'Canceled'),])?>
        </div>        
    </div>
    <div class="row">
            <div class="col-sm-1">
                <?=  Html::submitInput('Filtrar', ['class'=> 'btn btn-primary', 'id'=> 'filterButton'])?>
            </div>
            <div class="col-sm-1">
                <?= Html::a('Borrar Filtros', Url::to(['/checkout/payment/index']), ['class' => 'btn btn-default'])?>
            </div>
    </div>
    
    
</div>
<?php ActiveForm::end()?>

