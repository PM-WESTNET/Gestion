<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\checkout\models\search\PaymentMethodSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payment Methods');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-method-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                'modelClass' => Yii::t('app','Payment Method'),
            ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'payment_method_id',
            'name',
            [
                'attribute' => 'status',
                'value' => function($model){ return Yii::t('app',  ucfirst($model->status)); }
            ],
            'register_number:boolean',
            [
                'attribute'=>'type',
                'value'=>function($model){ return Yii::t('app',  ucfirst($model->type)); }
            ],

            [
                'class' => 'app\components\grid\ActionColumn',
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', 'Delete'),
                            'aria-label' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'class' => 'btn btn-danger'
                        ];
                        return $model->deletable ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options) : '';
                    }
                ]
            ],
        ],
    ]); ?>

</div>
