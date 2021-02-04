<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\modules\accounting\models\Account;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountMovement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="account-movement-form">

    <?php $form = ActiveForm::begin(); ?> 
    
    <div class="row">
        <div class="col-sm-12">
            <?= $form->errorSummary([$model, $item]); ?>

            <?php
                echo Html::activeHiddenInput($model, 'account_movement_id', ['value'=>$model->account_movement_id]);
                echo Html::activeHiddenInput($model, 'accounting_period_id', ['value'=>\app\modules\accounting\models\AccountingPeriod::getActivePeriod()->accounting_period_id]);
                echo Html::activeHiddenInput($model, 'status', ['value'=>\app\modules\accounting\models\AccountMovement::STATE_DRAFT]);
            ?>

            <?php echo app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

            <?php
                echo $this->render('@app/modules/partner/views/partner-distribution-model/_selector', ['model' => $model, 'form'=>$form]);
            ?>

            <?= $form->field($model, 'description')->textInput(['maxlength' => 150]) ?>

            <?= Html::activeHiddenInput($item, 'account_movement_item_id') ?>
            <?= Html::activeHiddenInput($item, 'account_movement_id') ?>
        </div>
    </div>
    <div class="row">
        
        <div class="col-sm-6">
            <div class="form-group">
                <label for="item-account-name" class="control-label"><?= Yii::t('app', 'Account') ?></label>
                <div class="col-sm-12">
                    <span class="form-control">
                        <?= $item->account->name ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-3 col-md-3">
            <?= $form->field($item, 'debit')->textInput(['placeholder' => Yii::t('app', 'Amount')])  ?>
        </div>
        <div class="col-sm-3 col-md-3">
            <?= $form->field($item, 'credit')->textInput(['placeholder' => Yii::t('app', 'Amount')])  ?>
        </div>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>