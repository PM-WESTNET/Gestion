<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\cobrodigital\models\search\PaymentCardFileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('cobrodigital', 'Payment Card Files');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-card-file-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('cobrodigital', 'Import Payment Card File'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'payment_card_file_id',
            'upload_date',
            'file_name',
            'path:ntext',

            ['class' => 'app\components\grid\ActionColumn'],
        ],
    ]); ?>
</div>
