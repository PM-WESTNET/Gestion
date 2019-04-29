
<?php

use app\modules\accounting\models\Account;
use app\modules\provider\models\ProviderBill;
use app\modules\provider\models\ProviderBillHasProviderPayment;
use app\modules\sale\models\Tax;
use app\modules\sale\models\TaxRate;
use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountConfig */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="panel panel-default" id="panel_bills">
    <div class="panel-heading">
        <h3 class="panel-title"><?= Yii::t('app', 'Bills') ?></h3>
    </div>
    <div class="panel-body">

        <?php $form = ActiveForm::begin([
            'id'=>'bill-add-form',
            'action' => ['add-bill', 'id' => $model->provider_payment_id],
            'options'=> ['onsubmit' =>'return false']
        ]); ?>
        <input type="hidden" name="ProviderBillHasProviderPayment[provider_payment_id]" value="<?=$model->provider_payment_id?>"/>

        <div class="row">
            <div class="col-sm-12 col-md-12">
                <?php
                $model2 = $model;
                \yii\widgets\Pjax::begin();
                echo GridView::widget([
                    'id'=>'grid_bills',
                    'dataProvider' => $dataProvider,
                    'options' => ['class' => 'table-responsive'],
                    'columns' => [
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'checkboxOptions' => function($model, $key, $index, $column) {
                                return [
                                    'value'     => $model['provider_bill_id'],
                                    'checked'   => ($model['total_amount']>0 ? true : false ),
                                    'class'     => 'checkbill'
                                ];
                            }
                        ],
                        [
                            'label' => Yii::t("app", "Bill"),
                            'value' => function($model){
                                return $model['bill_type'] ;
                            }
                        ],
                        [
                            'label' => Yii::t("app", "Number"),
                            'value' => function($model){
                                return $model['number'];
                            }
                        ],
                        [
                            'label' => Yii::t("app", "Date"),
                            'value' => function($model){
                                return Yii::$app->formatter->asDate($model['date']);
                            }
                        ],
                        'total:currency',
                        [
                            'label' => Yii::t("app", "Balance"),
                            'attribute' => 'balance',
                            'format' => 'currency'
                        ],
                        [
                            'label' => Yii::t("app", "Amount"),
                            'attribute' => 'total_amount',
                            'value' => function($model) use ($model2){
                                $total = $model2->amount-$model2->calculateTotalPayed();
                                $posible_amount = 0;
                                $rest = 0;
                                if($total > $model['balance']){
                                    $posible_amount = $model['balance'];
                                }
                                if($total < $model['balance']){
                                    $posible_amount = $total;
                                    $rest = $model['balance'] - $posible_amount;
                                }
                                if($total == $model['balance']){
                                    $posible_amount = $model['balance'];
                                }
                                return Html::textInput('total_amount', $model['total_amount'], [
                                    'class' => 'total_amount',
                                    'disabled' => ($model['total_amount']==0 ? true : false ),
                                    'data-max' => $posible_amount,
                                    'data-rest' => $rest
                                ]);
                            },
                            'format' => 'raw'
                        ],
                    ],
                    'options'=>[
                        'style'=>'margin-top:10px;'
                    ]
                ]);
                ?>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-sm-9 col-md-3">
                <label><?=Yii::t("app", "Total of Payment")?></label>
                <div><label><?=Yii::$app->formatter->asCurrency($model->amount)?></label></div>
            </div>
            <div class="col-sm-9 col-md-3">
                <label><?=Yii::t("app", "Total of Bills")?></label>
                <div><label><?=Yii::$app->formatter->asCurrency($model->calculateTotalPayed())?></label></div>
            </div>
            <div class="col-sm-9 col-md-3">
                <label><?=Yii::t("app", "Balance")?></label>
                <div><label><?=Yii::$app->formatter->asCurrency($model->amount-$model->calculateTotalPayed())?></label></div>
            </div>
        </div>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>
