<?php

use app\modules\checkout\models\PaymentMethod;
use app\modules\sale\models\Customer;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
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
        <div class="col-sm-4">
            <?= $form->field($model, 'bill_types')->checkboxList(ArrayHelper::map(BillType::find()->all(), 'bill_type_id', 'name'), ['separator' => '<br>']) ?>
            
            <?= Html::hiddenInput('BillSearch[bill_type_id]','') ?>
            
            <?= $form->field($model, 'statuses')->checkboxList([
                'draft' => Yii::t('app', 'Draft'),
                'completed' => Yii::t('app', 'Completed'),
                'closed' => Yii::t('app', 'Closed')
            ], ['separator' => '<br>']) ?>

            <?php //$form->field($model, 'payment_methods')->checkboxList(ArrayHelper::map(PaymentMethod::find()->all(), 'payment_method_id', 'name'), ['separator' => '<br>']) ?>

        </div>
        <div class="col-sm-8">
            
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group field-provider-account hidden-print">
                        <?=Html::label(Yii::t('app', "User"), ['user_id'])?>
                        <?= Select2::widget([
                            'model' => $model,
                            'attribute' => 'user_id',
                            'data' => yii\helpers\ArrayHelper::map(webvimark\modules\UserManagement\models\User::find()->all(), 'id', 'username' ),
                            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false, 'data-update-bill' => '' ],
                            'pluginOptions' => ['allowClear' => true],
                        ]);
                        ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <?= app\components\companies\CompanySelector::widget(['model' => $model, 'selection' => $model->company_id, 'inputOptions' => ['prompt' => Yii::t('app', 'All'), 'value'=> $model->company_id]]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
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
                <div class="col-sm-6">
                    
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
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'fromAmount', [
                        'template' => '{label}<div class="input-group" style="z-index:0;"><span class="input-group-addon">$</span>{input}</div>'
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'toAmount', [
                        'template' => '{label}<div class="input-group" style="z-index:0;"><span class="input-group-addon">$</span>{input}</div>'
                    ]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
            
                    <?= $form->field($model, 'currency_id')->dropDownList(
                            ArrayHelper::map(Currency::find()->all(), 'currency_id', 'name'),
                            ['prompt' => Yii::t('app', 'All')]
                    ) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'number', [
                        'template' => '{label}<div class="input-group" style="z-index:0;">{input}</div>'
                    ])->label('Numero de comprobante'); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="input-group" style="z-index:0;">
                            <?php
                                echo $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form'=> $form, 'model' => $model, 'attribute' => 'customer_id']);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'expired')->dropDownList([0 => Yii::t('app', 'Hide Expired'), 1 => Yii::t('app', 'Show Expired')], ['prompt' => Yii::t('app', 'All')]) ?>                    
                </div>
            </div>
        </div>
    </div>
    
    <hr>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
