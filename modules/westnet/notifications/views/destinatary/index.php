<?php

use app\modules\westnet\notifications\NotificationsModule;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = NotificationsModule::t('app', 'Destinataries');
$this->params['breadcrumbs'][] = ['label' => NotificationsModule::t('app', 'Notifications'), 'url' => ['notification/index']];
$this->params['breadcrumbs'][] = ['label' => $notification->name, 'url' => ['notification/view', 'id' => $notification->notification_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="destinatary-index">

    <h1>
        <?= Html::encode($this->title); ?>
    </h1>

    <p>
        <?=
        Html::a("<span class='glyphicon glyphicon-plus'></span> " . NotificationsModule::t('app', 'Create destinatary'), ['create', 'notification_id' => $notification->notification_id], ['class' => 'btn btn-success'])
        ;
        ?>
    </p>


    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'destinatary_id',
            [
                'attribute' => 'notification_id',
                'value' => function($model) {
                    return $model->notification->name;
                },
            ],
        [
            'class' => 'app\components\grid\ActionColumn',
        ]
        ],
    ]);
    ?>

</div>
