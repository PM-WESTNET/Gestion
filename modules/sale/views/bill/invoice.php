<?php

use app\modules\config\models\Config;
use kartik\grid\GridView;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\Contract */

$this->title = Yii::t('app', 'Batch Invoice');
$this->params['breadcrumbs'][] = Yii::t('app', 'Batch Invoice');
?>
<div class="alert alert-dismissible" role="alert" id="div-message" style="display: none;">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <div id="message"></div>
</div>

<div id="messages" style="height: 200px; overflow: auto; display: none; ">
</div>

<div class="batch-invoice">
    <div class="row">
        <div class="col-sm-12">
            <h1><?= Html::encode($this->title) ?></h1>
            <!-- Inicio Seleccion de datos para facturacion -->
            <div class="panel panel-default">
                <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-bill" aria-expanded="true" aria-controls="panel-body-bill">
                    <h3 class="panel-title"><?= Yii::t('app', 'Invoice Data') ?></h3>
                </div>
                <div class="panel-body collapse in" id="panel-body-bill" aria-expanded="true">

                    <?php $form = ActiveForm::begin(['id'=>'bill-form', 'method' => 'post']); ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form' => $form, 'model' => $searchModel, 'attribute' => 'customer_id']) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <?=$form->field($searchModel, 'period')->widget(DatePicker::classname(), [
                                'type' => 1,
                                'language' => Yii::$app->language,
                                'model' => $searchModel,
                                'attribute' => 'period',
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'dd-mm-yyyy',
                                ],
                                'options'=>[
                                    'class'=>'form-control filter dates',
                                    'placeholder'=>Yii::t('app','Date')
                                ]
                            ]);
                            ?>
                        </div>
                        <div class="col-sm-5">
                            <?=$form->field($searchModel, 'includePlan')->checkbox() ?>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group field-button">
                                <label>&nbsp;</label>
                                <?= Html::submitButton(Yii::t('app', 'Is Invoiced'), ['class' => 'btn btn-success form-control', 'id'=> 'btnInvoice', 'data-loading-text' =>  Yii::t('app', 'Processing')]) ?>
                            </div>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div> <!-- Fin Seleccion de datos para facturacion -->


        </div>
    </div>
</div>