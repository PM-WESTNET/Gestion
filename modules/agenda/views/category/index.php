<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \app\modules\agenda\AgendaModule::t('app', 'Task Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

   <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . \app\modules\agenda\AgendaModule::t('app', 'Create {modelClass}', [
                'modelClass' => \app\modules\agenda\AgendaModule::t('app', 'Category'),
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

            'category_id',
            'name',
            'description:ntext',
            'default_duration',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
