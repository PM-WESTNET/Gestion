<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Type */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_group_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\agenda\models\UserGroup::find()->all(), 'group_id', 'name'), ['encode' => false, 'separator' => '<br/>', 'prompt' => \app\modules\ticket\TicketModule::t('app', 'Select an option...')]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php if ($model->isNewRecord) : ?>
        <?= $form->field($model, 'slug')->textInput(['maxlength' => 45]) ?>
    <?php endif; ?>    

    <?=
    $form->field($model, 'duration')->widget(\kartik\time\TimePicker::classname(), [
        'pluginOptions' => [
            'defaultTime' => '02:00:00',
            'showMeridian' => false,
            'minuteStep' => 15,
            ''
        ]
    ]);
    ?>

    <div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

</div>
