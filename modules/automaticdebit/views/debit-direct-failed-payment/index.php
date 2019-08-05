<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\automaticdebit\models\search\DebitDirectFailedPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Debit Direct Failed Payments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="debit-direct-failed-payment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Debit Direct Failed Payment'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'debit_direct_failed_payment_id',
            'customer_code',
            'amount',
            'date',
            'cbu',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
