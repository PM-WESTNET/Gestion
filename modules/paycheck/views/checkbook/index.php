<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('paycheck', 'Checkbooks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="checkbook-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('paycheck','Checkbook')]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
        
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => Yii::t('paycheck', 'Money Box'),
                'value' => function ($model) {
                    return $model->moneyBoxAccount->moneyBox->name;
                }
            ],
            [
                'label' => Yii::t('paycheck', 'Money Box Account'),
                'value' => function ($model) {
                    return $model->moneyBoxAccount->number;
                }
            ],

            'start_number',
            'end_number',
            'last_used',
            'enabled',
        
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
