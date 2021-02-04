<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Period Closures');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="period-closure-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'PeriodClosure',
]), 
        ['create'], 
        ['class' => 'btn btn-success']) 
        ;?>
    </p>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'period_closure_id',
            'datetime:datetime',
            'cashier_id',
            'payment_count',
            'first_payout_number',
            // 'last_payout_number',
            // 'date',
            // 'time',
            // 'date_from',
            // 'date_to',
            // 'status',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
