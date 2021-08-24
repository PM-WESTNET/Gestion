<?php 
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Clientes por publicidad';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="payment-intention-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <hr>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'customer',
                'format' => 'raw',
                'label' => Yii::t('app', 'Customer'),
                'value' => function($model){
                    return Html::a($model->lastname . ' ' . $model->name . ' (' .$model->code . ')', 
                                ['/sale/customer/view', 'id' => $model->customer_id], 
                                ['class' => 'profile-link']);
                }
            ],
            [
                'attribute' => 'publicity_shape',
                'format' => 'raw',
                'label' => Yii::t('app', 'Publicity Shape'),
                'value' => function($model){
                	return strtoupper( Yii::t('app', $model->publicity_shape));
                }
            ],
        ],

    ]); ?>

</div>