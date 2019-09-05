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
                    'id' => 'management-form'
            ]); ?>

            <div class="hidden">
                <?= $form->field($model, 'ticket_id')->textInput([
                        'value' => $ticket_id
                ]); ?>

                <?= $form->field($model, 'user_id')->textInput([
                    'value' => Yii::$app->user->getId()
                ]); ?>
            </div>
            <?= $form->field($model, 'by_wp')->checkbox(); ?>
            <?= $form->field($model, 'by_sms')->checkbox(); ?>
            <?= $form->field($model, 'by_email')->checkbox(); ?>
            <?= $form->field($model, 'by_call')->checkbox(); ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Register ticket management'), [
                    'class' => 'btn btn-success pull-right',
                    'id' => 'management-submit-btn'
                ])?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
