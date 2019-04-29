<?php

use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('afip', 'Products for IIBB');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="taxes-book-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>


    </div>

    <div class="bill-search">

        <?php $form = ActiveForm::begin([
            'action' => ['iibb-products'],
            'method' => 'post',
            'id'     => 'mainForm'
        ]); ?>
        <input type="hidden" name="search" value="1" id="search"/>
        <div class="row">
            <div class="col-sm-12">
                <?= app\components\companies\CompanySelector::widget(['model'=>$searchModel]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($searchModel, 'fromDate')->widget(yii\jui\DatePicker::className(), [
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'date',
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
                    'attribute' => 'date',
                    'dateFormat' => 'dd-MM-yyyy',
                    'options'=>[
                        'class' => 'form-control dates',
                        'id' => 'to-date'
                    ]
                ]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php
                echo $form->field($searchModel, 'bill_types')->widget(Select2::classname(), [
                    'language' => 'es',
                    'data' => yii\helpers\ArrayHelper::map(app\modules\sale\models\BillType::find()->all(), 'bill_type_id', 'name'),
                    'options' => [
                        'multiple' => true,
                        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false, 'id'=>'bill_types'],
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php
                echo $form->field($searchModel, 'products')->widget(Select2::classname(), [
                    'language' => 'es',
                    'data' => $searchModel->findProducts(),
                    'options' => [
                        'multiple' => true,
                        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false, 'id'=>'products'],
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <?php

                echo Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary', 'type'=>'submit']);
                echo Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']);

                echo Html::button('<span class="glyphicon glyphicon-export"></span> '.Yii::t('accounting', 'Export'),
                    ['class' => 'btn btn-warning', 'type'=>'button',
                        'onclick' => '
                            $("#search").val(0);
                            $("#mainForm").submit();                        
                            setTimeout(function(){
                                $("#search").val(1);
                            }, 1000);
                        ',
                    ]);

                echo Html::a(Yii::t('app', 'Clear'), $form->action, ['class' => 'btn btn-default pull-right'])


            ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>


    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'showPageSummary' => true,
        'columns' => [
            [
                'label'=> Yii::t('app', 'Product'),
                'value' => function ($model){
                    return $model['product'];
                }
            ],
            [
                'label'=> Yii::t('app', 'Qty'),
                'value' => function ($model){
                    return $model['qty'];
                },
            ],
            [
                'label'=> Yii::t('app', 'total'),
                'format' => 'currency',
                'value' => function ($model) {
                    return $model['total'];
                },
                'pageSummary'=>true,
            ],
        ]
    ]); ?>

</div>