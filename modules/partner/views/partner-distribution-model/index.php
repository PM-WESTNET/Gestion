<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('partner', 'Partner Distribution Models');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-distribution-model-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
        'modelClass' => Yii::t('partner', 'Partner Distribution Model'),
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
                'label' => Yii::t('app', 'Company'),
                'attribute' => 'company.name',
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
