<?php

use app\modules\sale\models\TaxRate;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use app\modules\sale\models\BillType;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\BillSearch $searchModel
 */

$this->title = Yii::t('afip', "Book Buy" );
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bill-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row margin-top-full">
        <div class="col-xs-12">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
            ]); ?>

            <?= app\components\companies\CompanySelector::widget(['model'=>$searchModel]); ?>            
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($searchModel, 'fromDate')->widget(yii\jui\DatePicker::className(), [
                'language' => 'es',
                'model' => $searchModel,
                'dateFormat' => 'dd-MM-yyyy',
                'options'=>[
                    'class'=>'form-control dates',
                    'id' => 'from-date'
                ]
            ]);
            ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($searchModel, 'toDate')->widget(yii\jui\DatePicker::className(), [
                'language' => 'es',
                'model' => $searchModel,
                'dateFormat' => 'dd-MM-yyyy',
                'options'=>[
                    'class' => 'form-control dates',
                    'id' => 'to-date'
                ]
            ]);
            ?>
        </div>
    </div>
    <div class="row margin-bottom-full">
        <div class="col-xs-12">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning']) ?>
                <?= Html::a(Yii::t('app', 'Clear'), $form->action, ['class' => 'btn btn-info pull-right']) ?>
            </div>            
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <!-- <br/> -->
    
    <div class="row">
        <div class="col-xs-12">
            <?php
                $columns = [
                    [
                        'label'=> Yii::t('app', 'Business Name'),
                        'attribute' => 'business_name'
                    ],
                    [
                        'label'=> Yii::t('app', 'Document Number'),
                        'attribute' => 'tax_identification'
                    ],
                    [
                        'label'=> Yii::t('app', 'Bill'),
                        'value' => function($model) {
                            return $model['bill_type'] . " - " . $model['number'];
                        }
                    ],
                    [
                        'label'=> Yii::t('app', 'Date'),
                        'value' => function($model){
                            return Yii::$app->formatter->asDate($model['date']);
                        }
                    ],
                    [
                        'label'=> Yii::t('app', 'Subtotal'),
                        'value' => function($model){
                            return Yii::$app->formatter->asCurrency($model['net']);
                        }
                    ],

                ];
                foreach(TaxRate::find()->all() as $tax) {
                    $columns[] =             [
                        'label'=> Yii::t('app', $tax->tax->name . ' ' . ($tax->pct*100) . '%'),
                        'value' => function($model) use ($tax){
                            return Yii::$app->formatter->asCurrency($model[$tax->tax->name . ' ' . ($tax->pct*100) . '%']);
                        }
                    ];
                }

                $columns[] = [
                    'label'=> Yii::t('app', 'Total'),
                    'value' => function($model){
                        return Yii::$app->formatter->asCurrency($model['total']);
                    }
                ];

             echo ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'table-responsive'],        
                'columns' => $columns,
                'showConfirmAlert'=>false
            ]);
            ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => $columns,
            ]); ?>            
        </div>
    </div>

    <div class="row margin-top-full">
        <div class="col-xs-12 table-style">
            <?php
                foreach($searchModel->totals as $key=>$value) {
            ?>
                <div class="col-sm-4 font-s text-center">
                    <?= Yii::t('app', $key); ?>
                </div>
                <div class="col-sm-8 text-center font-bold">
                    <?= Yii::$app->formatter->asCurrency($value); ?>
                    
                </div>
            <?php } ?>  
        </div>
    </div>
</div>