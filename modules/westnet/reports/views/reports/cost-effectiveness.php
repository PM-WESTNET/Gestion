<?php

use app\modules\westnet\models\search\NodeSearch;
use app\modules\westnet\reports\ReportsModule;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $searchModel NodeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = ReportsModule::t('app', 'Cost effectiveness');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="customer-search">
        <?php $form = ActiveForm::begin(['method' => 'POST']); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::activeLabel($model, 'date_from'); ?>
                    <?php
                    echo yii\jui\DatePicker::widget([
                        'language' => Yii::$app->language,
                        'model' => $model,
                        'attribute' => 'date_from',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control filter dates',
                            'placeholder'=>Yii::t('app','Date')
                        ]
                    ]);
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::activeLabel($model, 'date_to'); ?>
                    <?php
                    echo yii\jui\DatePicker::widget([
                        'language' => Yii::$app->language,
                        'model' => $model,
                        'attribute' => 'date_to',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control filter dates',
                            'placeholder'=>Yii::t('app','Date')
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="row"><div class="col-md-12">&nbsp;</div></div>

    <div class="row">
        <div class="col-md-12 text-center">
            <?php
            echo \dosamigos\chartjs\ChartJs::widget([
                'type' => 'bar',
                'options' => [
                    'width' => 800,
                    'height' => 400,
                    'legend' => [
                            'position' => 'top',
                            'display' => 'true'
                    ]
                ],
                'data' => [
                    'labels' => $labels,
                    'datasets' => $datasets
                ]
            ]);
            ?>
        </div>
        <label><?= Yii::t('app', 'Earn').' : '.Yii::$app->formatter->asCurrency($earn) ?></label> <br>
        <label><?= Yii::t('app', 'Provider payments').' : '.Yii::$app->formatter->asCurrency($outgo) ?></label> <br>
        <label><?= Yii::t('app', 'Employee payments').' : '.Yii::$app->formatter->asCurrency($outgoEmployee) ?></label> <br>
        <label><?= 'Gastos bancarios : '.Yii::$app->formatter->asCurrency($account_movements) ?></label> <br>
        <label><?= ReportsModule::t('app', 'Cost effectiveness').' : '.Yii::$app->formatter->asCurrency($earn - $outgo - $account_movements) ?></label>
    </div>
    <div style="padding-top: 20px">
        <?= Html::label(Yii::t('app', 'References'))?>
        <p>
            1 - <?= Yii::t('app', 'Earn')?>: Se calcula el total cobrado por el período. (Pagos de clientes) <br>
            2 - <?= Yii::t('app', 'Provider payments')?> : Se calcula el total pagado por el período a partir de los pagos realizados a proveedores. <br>
            3 - <?= Yii::t('app', 'Employee payments')?> : Se calcula el total pagado por el período a partir de los pagos realizados a empleados. <br>
            4 - Gastos bancarios : Se calcula el total en las cuentas correspondientes a 'GASTOS BANCARIOS'. <br>
            5 - <?= ReportsModule::t('app', 'Cost effectiveness')?> : <?= Yii::t('app', 'Earn')?> - <?= Yii::t('app', 'Provider payments')?> - <?= Yii::t('app', 'Employee payments')?> - Gastos bancarios. <br>
                Importante:
            Sólo se incluyen retenciones o cualquier otro item pagado, si el mismo  ha sido incluído en un comprobante de pago.
        </p>
        <?= Html::label(Yii::t('app', 'Image References'))?>
        <div class="col-md-12">
            <div class="col-md-6" style="padding-top: 20px">
                <?= Html::img('@web/images/report-reference/Rentabilidad1.jpg', ['class' => 'img-responsive img-rounded align-center'])?>
            </div>
            <div class="col-md-6" style="padding-top: 20px">
                <?= Html::img('@web/images/report-reference/Rentabilidad2.jpg', ['class' => 'img-responsive img-rounded align-center'])?>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-md-6" style="padding-top: 20px">
                <?= Html::img('@web/images/report-reference/ClientesTotalesActivos11.png', ['class' => 'img-responsive img-rounded align-center'])?>
            </div>
            <div class="col-md-6" style="padding-top: 20px">
                <?= Html::img('@web/images/report-reference/ClientesSumados.jpg', ['class' => 'img-responsive img-rounded'])?>
            </div>
        </div>
    </div>
</div>