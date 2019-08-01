<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\automaticdebit\models\AutomaticDebitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Automatic Debits');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="automatic-debit-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Automatic Debit'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'customer_id',
                'value' => function ($model) {
                    return $model->customer->fullName;
                }
            ],
            [
                'attribute' => 'bank_id',
                'value' => function ($model) {
                    return $model->bank->name;
                }
            ],
            'cbu',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->getStatusLabel();
                }
            ],

            ['class' => 'app\components\grid\ActionColumn'],
        ],
    ]); ?>
</div>