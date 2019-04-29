<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
?>

<div class="row hidden-print">
    <div class="row">
        <div class="col-sm-3<?php if (!Yii::$app->params['companies']['enabled']) echo ' hidden' ?>">

            <?php
            $companies = [];
            foreach ($model->billType->companies as $company) {
                if ($company->parent_id) {
                    $companies[] = $company->company_id;
                }
            }
            echo app\components\companies\CompanySelector::widget(['model' => $model, 'showCompanies' => 'children', 'conditions' => [
                    'status' => 'enabled', 'company_id' => $companies,
            ]]);
            ?>

        </div>
        <div class="col-sm-2">

            <?=
            $form->field($model, 'bill_type_id')->dropDownList(ArrayHelper::map($model->company->billTypes, 'bill_type_id', 'name'), [
                'id' => 'bill-type',
                'data-update-bill' => ''
            ])
            ?>

        </div>

        <div class="col-sm-<?= $model::$expirable ? 5 : 7 ?>">

            <?= $this->render('_customer-selector', ['model' => $model]); ?>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-3">
            <?=
            $form->field($model, 'point_of_sale_id')->dropDownList(ArrayHelper::map($model->company->pointsOfSale, 'point_of_sale_id', 'fullname'), [
                'id' => 'point_of_sale',
                'data-update-bill' => ''
            ])
            ?>
        </div> 
        <?php if ($electronic_billing == 0) { ?>
            <div class="col-sm-6">
                <?= $form->field($model, 'number')->textInput(['data-bill-update' => '', 'id' => 'bill-number']) ?>
            </div>
        <?php } ?>
        <div class="col-sm-3">
            <?php
            if ($electronic_billing == 0) {

                echo $form->field($model, 'date', ['template' => "{label}<br>{input}"])->widget(\yii\jui\DatePicker::class, [
                    'class' => 'form-control',
                    'dateFormat' => 'dd-MM-yyyy',
                    'options' => [
                        'data-update-bill' => '',
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        'maxDate' => 0,
                        'minDate' => ((new DateTime('1 week ago'))->format('dd-MM-yyyy')),
                    ],
                ]);
            }
            ?>
        </div>
    </div>
</div>

<div class="row hidden-print">
    <?php if ($model::$expirable): ?>
        <div class="col-sm-12" style="z-index: 2;">

            <?=
            $form->field($model, 'expiration')->widget(yii\jui\DatePicker::className(), [
                'language' => Yii::$app->language,
                'model' => $model,
                'attribute' => 'expiration',
                'dateFormat' => 'dd-MM-yyyy',
                'options' => [
                    'class' => 'form-control dates',
                    'data-update-bill' => ''
                ],
            ]);
            ?>

        </div>
    <?php endif; ?>
    <div class="col-sm-12">
        <!-- <div class="input-group"> -->
        <label class="control-label"><?= Yii::t('app', 'Observations') ?></label>
        <?= Html::activeTextInput($model, 'observation', ['class' => 'form-control', 'id' => 'observation', 'maxlength' => 250, 'data-changed' => 'false']) ?>
        <!-- </div> -->
    </div>
</div>

<?php
/* * ***********************  
 * *
 * *  PRINTABLE VERSION 
 * * 
 * *********************** */
?>

<div class="row visible-print">

    <table class="table table-bordered">
        <tr>
            <td colspan="5"><?= $model->company ? $model->company->name : null ?></td>
        </tr>
        <tr>
            <td style="width: 20%;"><?= Yii::t('app', 'Customer') ?></td>
            <td style="width: 20%;"><?= Yii::t('app', 'Date') ?></td>
            <td style="width: 20%;"><?= Yii::t('app', 'Time') ?></td>
            <td style="width: 20%;"><?= Yii::t('app', 'Status') ?></td>
            <?php if ($model::$expirable): ?>
                <td style="width: 20%;"><?= Yii::t('app', 'Expiration') ?></td>
            <?php endif; ?>
        </tr>
        <tr style="font-weight: bold;">
            <td><strong><?= $model->customer ? $model->customer->fullName : ''; ?></strong></td>
            <td><?= Yii::$app->formatter->asDate($model->date); ?></td>
            <td><?= $model->time; ?></td>
            <td><?= Yii::t('app', ucfirst($model->status)); ?></td>
            <?php if ($model::$expirable): ?>
                <td><?= $model->expiration ?></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td colspan="5"><strong><?= Yii::t('app', 'Observations') ?>: </strong> <?= $model->observation ?></td>
        </tr>
    </table>

    <?php if ($model::$expirable): ?>
        <div class="col-sm-2">

            <?= $model->expiration ?>

        </div>
    <?php endif; ?>

</div>
