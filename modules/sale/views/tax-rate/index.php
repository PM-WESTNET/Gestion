<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sale\models\search\TaxRateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tax Rates');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-rate-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('app', 'Tax Rate')]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'tax_id',
                'value'=>function($model){ if(!empty($model->tax)) return $model->tax->name; }
            ],        
            [
                'attribute' => 'pct',
                'value' => function ($model) { return $model->name; }
            ],
            ['attribute' => 'code'],
            [
                'class' => 'app\components\grid\ActionColumn', 
            ],
        ],
    ]); ?>

</div>
