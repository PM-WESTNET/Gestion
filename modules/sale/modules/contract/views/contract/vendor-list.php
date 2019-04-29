<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\sale\modules\contract\models\Contract;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sale\models\search\ContractSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'My Sales');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?> <small><?= "$vendor->name $vendor->lastname" ?></small></h1>
    </div>

    <p>
    </p>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'contract_id',
            [
                'attribute' => 'customer_id',
                'value' => function($model){ return $model->customer->fullName; },
                'filter' => $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['model' => $searchModel, 'attribute' => 'customer_id'])
            ],
            [
                'attribute' => 'from_date',
                'filter' => false
            ],
            [
                'attribute' => 'to_date',
                'filter' => false
            ],
            [
                'attribute' => 'status',
                'value' => function($model){ return Yii::t('app', ucfirst($model->status)); },
                'filter' => Contract::getStatusesForSelect()
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

</div>
