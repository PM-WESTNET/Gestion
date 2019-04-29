<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('partner', 'Partners');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
        'modelClass' => Yii::t('partner', 'Partner'),
    ]), 
            ['create'], 
            ['class' => 'btn btn-success']) 
            ;?>
        </p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            [
                'label' => Yii::t('accounting', 'Accounting Account'),
                'attribute' => 'account.name',
            ],
            [
                'header' => Yii::t('partner','Account Status'),
                'format' => 'html',
                'value' => function($model){ return Html::a('<span class="glyphicon glyphicon-eye-open"></span> '.Yii::t('app','View'),
                    ['partner/account','id'=>$model->partner_id], ['class'=>'btn btn-width btn-default']); }
            ],
            [
                'header' => Yii::t('partner','Input'),
                'format' => 'html',
                'value' => function($model){ return Html::a('<span class="glyphicon glyphicon-download"></span> '.Yii::t('partner','Input'),
                    ['partner/input','id'=>$model->partner_id], ['class'=>'btn btn-width btn-default']); }
            ],
            [
                'header' => Yii::t('partner','Withdraw'),
                'format' => 'html',
                'value' => function($model){ return Html::a('<span class="glyphicon glyphicon-upload"></span> '.Yii::t('partner','Withdraw'),
                    ['partner/withdraw','id'=>$model->partner_id], ['class'=>'btn btn-width btn-default']); }
            ],
            [
                'header' => Yii::t('accounting','Movements'),
                'format' => 'html',
                'value' => function($model){
                        return Html::a('<span class="glyphicon glyphicon-th-list"></span> '.Yii::t('accounting','Movements'),
                            ['partner/movements','id'=>$model->partner_id, 'AccountMovementSearch[account_id_from]'=>$model->account_id, 'AccountMovementSearch[account_id_to]'=>$model->account_id], ['class'=>'btn btn-width btn-default']);
                }

            ],
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
