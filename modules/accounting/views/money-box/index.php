<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('accounting', 'Money Boxes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-box-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('accounting','Money Box')]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
                'attribute' => 'moneyBoxType.name',
                'label'     => Yii::t('accounting', 'Money Box Type')
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
            ]
        ],
    ]); ?>

</div>
