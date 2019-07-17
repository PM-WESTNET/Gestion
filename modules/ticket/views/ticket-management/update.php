<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\TicketManagement */

$this->title = Yii::t('app', 'Update Ticket Management: ' . $model->ticket_management_id, [
    'nameAttribute' => '' . $model->ticket_management_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Managements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ticket_management_id, 'url' => ['view', 'id' => $model->ticket_management_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ticket-management-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
