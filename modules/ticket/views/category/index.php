<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\ticket\TicketModule;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Ticket Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . TicketModule::t('app', 'Create category'), 
            ['create'], 
            ['class' => 'btn btn-success']) 
            ;?>
        </p>
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'header'=> TicketModule::t('app', 'Parent'),
                'value'=>function($model){
                    return ($model->parent ? $model->parent->name: '' );
                }
            ],
            'name',
            [
                'header'=> TicketModule::t('app', 'Notify'),
                'value'=>function($model){
                    return Yii::t('app', ($model->notify ?  'Yes' : 'No' ));
                }
            ],
            'description:ntext',
            'slug',
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
