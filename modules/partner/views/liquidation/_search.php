<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\provider\models\search\ProviderBillSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provider-bill-search">

    <?php $form = ActiveForm::begin([
        'action' => ['liquidation/liquidation-items', 'PartnerLiquidationSearch[partner_liquidation_id]' => $model->partner_liquidation_id ],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'fromDate')->widget(yii\jui\DatePicker::className(),[
        'language' => Yii::$app->language,
        'dateFormat' => 'dd-MM-yyyy',
        'options'=>[
            'class'=>'form-control filter dates',
            'placeholder'=>Yii::t('partner','fromDate')
        ]
    ]);
    ?>

    <?= $form->field($model, 'toDate')->widget(yii\jui\DatePicker::className(),[
        'language' => Yii::$app->language,
        'dateFormat' => 'dd-MM-yyyy',
        'options'=>[
            'class'=>'form-control filter dates',
            'placeholder'=>Yii::t('partner','toDate')
        ]
    ]);
    ?>


    <?= $form->field($model, 'type')->dropDownList([
            '0'=> 'Todos',
            'Movimiento'=> 'Movimiento',
            'Cobro'=> 'Cobro',
            'Pago'=> 'Pago'
    ]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
