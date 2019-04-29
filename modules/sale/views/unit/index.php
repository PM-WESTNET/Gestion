<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\UnitSearch $searchModel
 */

$this->title = Yii::t('app', 'Units');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unit-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
      'modelClass' => Yii::t('app','Unit'),
    ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'unit_id',
            'name',
            'type',
            'symbol',
            'symbol_position',

            ['class' => 'app\components\grid\ActionColumn'],
        ],
    ]); ?>

</div>
