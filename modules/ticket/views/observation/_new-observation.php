<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 14/03/19
 * Time: 17:20
 */

use app\modules\ticket\TicketModule;
use yii\widgets\ActiveForm;
use app\modules\ticket\models\Observation;
use yii\helpers\Html;

?>

<div class="row">
    <div class="col-lg-12">

        <div class="well well-lg">

            <?php $form = ActiveForm::begin([
                    'id' => 'observation-form'
            ]); ?>

            <?= $form->field($model, 'title'); ?>

            <?= $form->field($model, 'description')->textarea([
                'rows' => 10
            ]); ?>

            <div class="hidden">
                <?= $form->field($model, 'ticket_id')->textInput([
                    'value' => $ticket_id
                ]); ?>

                <?= $form->field($model, 'user_id')->textInput([
                    'value' => Yii::$app->user->getId()
                ]); ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton(TicketModule::t('app', 'Create observation'), [
                    'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success',
                    'id' => 'observation-submit-btn'
                ])?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
