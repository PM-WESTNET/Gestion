<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\TicketManagement */

$this->title = Yii::t('app', 'Create Ticket Management');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Managements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-management-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
