<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sale\modules\contract\models\search\ProgrammaticChangePlanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Programmatic Change Plans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programmatic-change-plan-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => Yii::t('app','Customer'),
                'value' => function ($model){
                    return $model->contract->customer->fullName . ' ('.$model->contract->customer->code. ')';
                },
                'filter' => $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['model' => $searchModel, 'attribute' => 'customer_id'])
            ],
            [
                'attribute' => 'date',
                'filter' => \kartik\date\DatePicker::widget([
                    'name' => 'ProgrammaticChangePlanSearch[date]',
                    'value' => $searchModel->date,
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy'
                    ]
                ])
            ],
            'applied:boolean',
            [
                'label' => Yii::t('app','Plan'),
                'value' => function ($model){
                    return $model->product->name;
                },
                'filter' => \kartik\select2\Select2::widget([
                    'name' => 'ProgrammaticChangePlanSearch[product_id]',
                    'value' => $searchModel->product_id,
                    'data' => \yii\helpers\ArrayHelper::map(\app\modules\sale\models\Product::find()
                        ->andWhere(['type' => 'plan', 'status' => 'enabled'])->all(),'product_id', 'name'),
                    'options' => ['placeholder' => Yii::t('app','Select an option')]
                ])
            ],
            [
                'attribute' => 'User',
                'value' => function ($model){
                    return $model->user->username;
                },
                'filter' => \kartik\select2\Select2::widget([
                    'name' => 'ProgrammaticChangePlanSearch[user_id]',
                    'value' => $searchModel->user_id,
                    'data' => \yii\helpers\ArrayHelper::map(\webvimark\modules\UserManagement\models\User::find()->all(), 'id', 'username'),
                    'options' => ['placeholder' => Yii::t('app','Select an option')]
                ])
            ],

            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{view} {delete}'
            ],
        ],
    ]); ?>
</div>
