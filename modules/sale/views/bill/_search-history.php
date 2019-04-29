<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\BillType;
use app\modules\sale\models\Currency;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\search\BillSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="bill-search">

    <?php $form = ActiveForm::begin([
        //'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-5">
            <?= $form->field($model, 'bill_types')->checkboxList(ArrayHelper::map(BillType::find()->all(), 'bill_type_id', 'name'), ['separator' => '<br>']) ?>
            
            <?= Html::hiddenInput('BillSearch[bill_type_id]','') ?>
            
            <?= $form->field($model, 'statuses')->checkboxList([
                'draft' => Yii::t('app', 'Draft'),
                'completed' => Yii::t('app', 'Completed'),
                'closed' => Yii::t('app', 'Closed')
            ], ['separator' => '<br>']) ?>
        </div>
        <div class="col-sm-6 no-padding">
            <?= app\components\companies\CompanySelector::widget(['model' => $model, 'inputOptions' => ['prompt' => Yii::t('app', 'All')]]) ?>
            
            <div class="row">
                <div class="col-sm-6 no-padding">
                    <?= $form->field($model, 'fromDate')->widget(yii\jui\DatePicker::className(), [
                        'language' => Yii::$app->language,
                        'model' => $model,
                        'attribute' => 'date',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control dates',
                            'id' => 'from-date'
                        ]
                    ]);
                    ?>
                </div>
                <div class="col-sm-6 no-padding">
                    <?= $form->field($model, 'toDate')->widget(yii\jui\DatePicker::className(), [
                        'language' => Yii::$app->language,
                        'model' => $model,
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
            <?= $form->field($model, 'fromAmount', [
                'template' => '{label}<div class="input-group" style="z-index:0;"><span class="input-group-addon">$</span>{input}</div>'
            ]) ?>
            
            <?= $form->field($model, 'toAmount', [
                'template' => '{label}<div class="input-group" style="z-index:0;"><span class="input-group-addon">$</span>{input}</div>'
            ]) ?>
            
            <?= $form->field($model, 'currency_id')->dropDownList(
                    ArrayHelper::map(Currency::find()->all(), 'currency_id', 'name'),
                    ['prompt' => Yii::t('app', 'All')]
            ) ?>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-sm-5 col-sm-offset-1">
            <?= $form->field($model, 'granularity')->dropDownList([
                'daily'=>Yii::t('app','Daily summary'),
                'monthly'=>Yii::t('app','Monthly summary'),
                'yearly'=>Yii::t('app','Yearly summary'),
            ]); ?>
        </div>
        <div class="col-sm-5">
            <?= Html::label(Yii::t('app','Chart')); ?>
            <?= Html::activeDropDownList($model, 'chartType', [
                false=>Yii::t('app','None'),
                'line'=>Yii::t('app','Line'),
                'bar'=>Yii::t('app','Bar'),
                'radar'=>Yii::t('app','Radar'),
            ],['class'=>'form-control filter']);
            ?>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
