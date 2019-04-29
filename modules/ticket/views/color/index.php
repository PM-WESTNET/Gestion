<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\ticket\TicketModule;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Ticket Colors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="color-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . TicketModule::t('app', 'Create Color'),
            ['create'], 
            ['class' => 'btn btn-success']) 
            ;?>
        </p>
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'color_id',
            'color',
            'order',
            'name',
            'slug',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
