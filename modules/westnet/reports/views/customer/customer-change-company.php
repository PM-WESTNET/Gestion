<?php

use Mpdf\Tag\Form;
use yii\helpers\Html;
use yii\jui\DatePicker;
use kartik\grid\GridView;
use yii\grid\SerialColumn;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView as GridGridView;
use app\components\companies\CompanySelector;
use app\modules\westnet\reports\ReportsModule;

$this->title = ReportsModule::t('app', 'Customers change company history');

?>
<div class="customer_change_company">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    
    <div class="">
        <?php $form = ActiveForm::begin(['method' => 'GET']); ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field( $search, 'date_from')->widget( DatePicker::class, [
                    'dateFormat' => 'dd-MM-yyyy',
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]);
                ?>
            </div>
            <div class="col-md-4 ">
                <?= $form->field( $search, 'date_to')->widget( DatePicker::class, [
                    'dateFormat' => 'dd-MM-yyyy',
                    'options'=>[
                        'class'=>'form-control filter dates',
                        'placeholder'=>Yii::t('app','Date')
                    ]
                ]);
                ?>
            </div>
            <div class="col-md-4">
                <?= $this->render('@app/modules/sale/views/customer/_find-with-autocomplete',[
                    'form' => $form,
                    'model' => $search,
                    'attribute' => 'customer_id',
                    'options'=>[
                        'class'=>'form-control',
                        'placeholder'=>Yii::t('app','Search')
                    ]
                ])
                ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <?= CompanySelector::widget([
                    'model' => $search,
                    'attribute' => 'company_id_from',
                    'inputOptions' => ['prompt'=>'Select...']

                ])?>
                
            </div>
            <div class="col-md-4">
                <?= CompanySelector::widget([
                    'model' => $search,
                    'attribute' => 'company_id_to',
                    'inputOptions' => ['prompt'=>'Select...']
                ])?>
                
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>

        <?php ActiveForm::end()?>

    </div>

    <?= GridView::widget([
        'dataProvider' => $data
        //,'showFooter' => true
        ,'columns' => [
            [
                'class' => SerialColumn::class 
            ],[
                'label'=> Yii::t('app', 'Customer'),
                'value'=>function($model){
                    return  $model->customer->fullname .'(' . $model->customer->code. ')';
                },
            ],[
                'label'=> ReportsModule::t('app', 'Date'),
                'value'=>function($model){
                    return  Yii::$app->formatter->asDateTime( $model->created_at, 'dd-MM-yyyy H:mm ');
                },
            ],[
                'label'=> ReportsModule::t('app', 'Company from'),
                'value'=>function($model){
                    return $model->oldCompany->name;
                },
            ],[
                'label'=> ReportsModule::t('app', 'Company to'),
                'value'=>function($model){
                    return $model->newCompany->name;
                },
            ]
        ]
    ]);
    ?>
    
</div>