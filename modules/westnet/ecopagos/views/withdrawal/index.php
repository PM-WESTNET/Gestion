<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Withdrawals');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdrawal-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?=
        Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
                    'modelClass' => 'Withdrawal',
                ]), ['create'], ['class' => 'btn btn-success'])
        ;
        ?>
    </p>


    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'withdrawal_id',
            'daily_closure_id',
            'cashier_id',
            'amount',
            'datetime',
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]);
    ?>

</div>
