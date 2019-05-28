<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \app\modules\ticket\TicketModule;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Ticket */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tickets'), 'url' => ['open-tickets']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->ticket_id]];
$this->params['breadcrumbs'][] = TicketModule::t('app', 'Observations');
?>
<div class="ticket-view">

    <h1>
        <?= TicketModule::t('app', 'Observations'); ?>
        <small style="color: <?= $model->color->color; ?>;">[<?= $model->number; ?>] <?= Html::encode($model->title); ?></small>
    </h1>

    <!-- Ticket information -->
    <div class="row">
        <div class="col-lg-12">
            <?= $this->render("_ticket_info", [
                    'model' => $model,
                    'observations' => $currentObservations,
                    'pages' => $pagination
            ]); ?>
        </div>
    </div>
    <!-- end Ticket information -->

    <?php if ($model->statusIsActive()) : ?>
        <!-- Observation form -->
        <div class="row">
            <div class="col-lg-12">

                <div class="well well-lg">

                    <h4 class="margin-bottom-half">
                        <?= TicketModule::t('app', 'Add observation'); ?>
                    </h4>

                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($observation, 'title'); ?>

                    <?= $form->field($observation, 'description')->textarea([
                        'rows' => 10
                    ]); ?>

                    <div class="form-group">
                        <?= Html::submitButton(TicketModule::t('app', 'Create observation'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        <!-- end Observation form -->
    <?php else : ?>
        <div class="row">
            <div class="col-lg-12">
                <p>
                    <?= TicketModule::t('app', 'Ticket must be active to make an observation on this ticket.'); ?>
                </p>
            </div>
        </div>
    <?php endif; ?>
</div>
