<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Ticket */

$this->title = \app\modules\ticket\TicketModule::t('app', 'History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tickets'), 'url' => ['open-tickets']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->ticket_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-view">

    <h1>
        <span style="color: <?= $model->color->color; ?>;">
            [<?= $model->number; ?>]
            <?= Html::encode($model->title); ?> 
        </span>
        <small>[<?= $model->status->name; ?>]</small>
    </h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'history_id',
            [
                'attribute' => 'title',
                'value' => function($model) {
                    return \app\modules\ticket\TicketModule::t('app', $model->title);
                }
            ],
            'date',
            'time',
            [
                'attribute' => 'user',
                'value' => 'user.username'
            ],
        ],
    ]);
    ?>

</div>
