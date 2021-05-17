<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Collapse;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sale\modules\contract\models\search\ProgrammedPlanChangeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Programmed plan changes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programmed-plan-change-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
                    'modelClass' => Yii::t('app','Programmed plan change'),]),
                ['create'],
                ['class' => 'btn btn-success pull-right']
            )?>
        </p>
    </div>

    <div>
        <?= Collapse::widget([
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters'),
                    'content' => $this->render('_search', ['model' => $searchModel]),
                    'encode' => false,
                ],
            ],
            'options' => [
                'class' => 'print',
                'aria-expanded' => 'false'
            ]
        ]);
        ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => Yii::t('app','Customer'),
                
                'value' => function ($model){
                    // if contract table is null, do nothing
                    if (isset($model->contract->customer)) {
                        // simplifying varibable's names
                        $customerModel = $model->contract->customer;

                        return  Html::a($customerModel->fullName . ' ('.$customerModel->code. ')', ['/sale/customer/view', 'id' => $model->contract->customer_id]);
                        return $customerModel->fullName . ' ('.$customerModel->code. ')';
                    } 
                    else{
                        yii::warning("contract table is null");
                    }
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'date',
            ],
            'applied:boolean',
            [
                'label' => Yii::t('app','Plan'),
                'value' => function ($model){
                    return $model->product->name;
                },
            ],
            [
                'attribute' => 'user_id',
                'value' => function ($model){
                    // Check if table username is null
                    if (isset($model->user)){
                        return $model->user->username;
                    }
                    else{
                        yii::warning("contract table is null");
                    }
                },
            ],

            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{view} {delete}'
            ],
        ],
    ]); ?>
</div>
