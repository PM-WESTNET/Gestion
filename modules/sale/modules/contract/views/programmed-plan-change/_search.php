<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\modules\sale\modules\contract\models\Plan;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;
use webvimark\modules\UserManagement\models\User;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\search\ProgrammedPlanChangeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="programmatic-change-plan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['model' => $model, 'attribute' => 'customer_id', 'form' => $form])?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'product_id')->widget(Select2::class, [
                'data' => ArrayHelper::map(Plan::find()->all(), 'product_id', 'name'),
                'pluginOptions' => [
                    'placeholder' => Yii::t('app', 'Select ...')
                ]
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'user_id')->widget(Select2::class, [
                'data' => ArrayHelper::map(User::find()->all(), 'id', 'username'),
                'pluginOptions' => [
                    'placeholder' => Yii::t('app', 'Select ...')
                ]
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'date')->widget(DatePicker::class, [
                'pluginOptions' => ['format' => 'dd-mm-yyyy']
            ])?>
        </div>
        <div class="col-sm-6" style="margin-top: 30px">
            <?= $form->field($model, 'applied')->checkbox() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
