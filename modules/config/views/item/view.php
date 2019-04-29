<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\config\ConfigModule;

/* @var $this yii\web\View */
/* @var $model app\modules\config\models\Item */

$this->title = $model->attr;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Config Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . ConfigModule::t('config', 'Update'), ['update', 'id' => $model->item_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . ConfigModule::t('config', 'Add Validator'), ['rule/create', 'item' => $model->item_id], ['class' => 'btn btn-success']) ?>
            <?php if($model->deletable) echo Html::a('<span class="glyphicon glyphicon-remove"></span> ' . ConfigModule::t('config', 'Delete'), ['delete', 'id' => $model->item_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => ConfigModule::t('config', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>
    
    <?php
    $validators = '';
    foreach ($model->rules as $i => $rule){
        $validators .= Html::a($rule->validator, ['rule/view', 'id' => $rule->rule_id]);
        if($i < count($model->rules) - 1 ){
            $validators .= ', ';
        }
    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'item_id',
            'attr',
            'type',
            'default',
            'label',
            'description',
            [
                'attribute' => 'category_id',
                'value' => $model->category->name
            ],
            'superadmin:boolean',
            'multiple:boolean',
            [
                'label' => ConfigModule::t('config', 'Validators'),
                'value' => $validators,
                'format' => 'html'
            ]
        ],
    ]) ?>
    
    <h2><?= ConfigModule::t('config', 'Validators') ?></h2>
    <?php 
    $dataProvider = new yii\data\ActiveDataProvider([
        'query' => $model->getRules()
    ]);
    ?>
    
    <?= yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => false,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'rule_id',
            'validator',
            'message',

            [
                'class' => 'app\components\grid\ActionColumn',
                'controller' => 'rule',
            ],
        ],
    ]); ?>
    

</div>
