<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sale\models\search\TaxConditionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tax Conditions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-condition-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('app', 'Tax Condition')]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'tax_condition_id',
            'name',
            [
                'attribute'=> 'billTypes',
                'value'=>function($model){ return $model->getBillTypesNames('<br/>'); },
                'format' => ['html']
            ],
            [
                'attribute'=> 'billTypesBuy',
                'value'=>function($model){ return $model->getBillTypesBuyNames('<br/>'); },
                'format' => ['html']
            ],
                    [
                'label'=> Yii::t('app', 'Document type required'),
                'value'=>function($model){ return $model->getDocumentTypesLabels(); }
            ],        
            'exempt:boolean',
            [
                'class' => 'app\components\grid\ActionColumn', 
            ],
        ],
    ]); ?>

</div>
