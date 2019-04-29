<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Commissions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="commission-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Commission',
]), 
        ['create'], 
        ['class' => 'btn btn-success']) 
        ;?>
    </p>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'commision_id',
            [
                'header'=> 'Ecopago',
                'value'=>function($model){ if(!empty($model->ecopago)) return $model->ecopago->name; }
            ],        
                                'create_datetime:datetime',
            'update_datetime:datetime',
            'type',
            'value',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
