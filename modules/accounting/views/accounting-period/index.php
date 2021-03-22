<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('accounting', 'Accounting Periods');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accounting-period-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
        'modelClass' => Yii::t('accounting', 'Accounting Period'),    ]), 
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
            'number',
            'date_from',
            'date_to',
            [
                'label' => Yii::t('app', 'Status'),
                'value' => function ($model) {
                    return Yii::t('accounting', ucfirst($model->status));
                }
            ],

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
