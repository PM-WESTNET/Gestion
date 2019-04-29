<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\pagomiscuentas\models\search\PagomiscuentasLiquidationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Pagomiscuentas Liquidations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pagomiscuentas-liquidation-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'PagomiscuentasLiquidation',
]), 
        ['create'], 
        ['class' => 'btn btn-success']) 
        ;?>
    </p>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'pagomiscuentas_liquidation_id',
            'file',
            'created_at',
            'updated_at',
            'number',
            // 'account_movement_id',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
