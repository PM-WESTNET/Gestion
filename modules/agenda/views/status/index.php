<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \app\modules\agenda\AgendaModule::t('app', 'Task Statuses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="status-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . \app\modules\agenda\AgendaModule::t('app', 'Create {modelClass}', [
                'modelClass' => \app\modules\agenda\AgendaModule::t('app', 'Task Status'),
            ]), 
            ['create'], 
            ['class' => 'btn btn-success']) 
            ;?>
        </p>
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'description:ntext',
            'color',
            'slug',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
