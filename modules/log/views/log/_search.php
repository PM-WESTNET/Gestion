<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\log\models\Log;
use kartik\widgets\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\search\LogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="log-search">

    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
    ]);
    ?>
    <div>
        <div class="col-sm-6">
            <?=
            $form->field($model, 'route')->widget(\kartik\widgets\Select2::classname(), [
                'data' => Log::getLogClassNames(),
                'options' => ['placeholder' => Yii::t('app', 'Search'), 'multiple' => false],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
            ?>
        </div>
        <div class="col-sm-6">
             <?=
            $form->field($model, 'user_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => ArrayHelper::map(User::find()->all(), 'id', 'username'),
                'options' => ['placeholder' => Yii::t('app', 'Search'), 'multiple' => false],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <label class="control-label"><?= Yii::t('app', 'From Date') ?></label>
            <?=
            DatePicker::widget([
                'model' => $model,
                'attribute' => 'fromDate',
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-sm-6">
            <label class="control-label"><?= Yii::t('app', 'To Date') ?></label>
            <?=
            DatePicker::widget([
                'model' => $model,
                'attribute' => 'toDate',
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true
                ],
            ]);
            ?>
        </div>

    </div>
    <br>
    <div>
        <?= $form->field($model, 'search_text')->textInput() ?>

    </div>
    <br>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
