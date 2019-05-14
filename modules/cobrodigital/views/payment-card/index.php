<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\cobrodigital\models\search\PaymentCardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('cobrodigital', 'Payment Cards');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-card-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'payment_card_id',
            'payment_card_file_id',
            'code_19_digits',
            'code_29_digits',
            'used:boolean',

            ['class' => 'app\components\grid\ActionColumn'],
        ],
    ]); ?>
</div>
