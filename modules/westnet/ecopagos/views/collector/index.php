<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Collectors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="collector-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Create Collector'), ['create'], ['class' => 'btn btn-success']); ?>
        </p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'lastname',
            'collector_id',
            'address_id',
            'number',
            // 'document_number',
            // 'document_type',
            // 'limit',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
