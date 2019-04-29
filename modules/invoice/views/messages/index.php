<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\UnitSearch $searchModel
 */

$this->title = Yii::t('afip', 'Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unit-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'type',
                'content'=>function($model, $key, $index){
                    return $model->getType();
                }
            ],
            'code',
            'description',
        ],
    ]); ?>

</div>
