<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\ProfileClassSearch $searchModel
 */

$this->title = Yii::t('app', 'Profile Classes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-class-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
      'modelClass' => Yii::t('app','Profile Class'),
    ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>

    <?php $grid = GridView::begin([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'profile_class_id',
            'name',
            'data_type',
            'data_max',
            [
                'attribute'=>'status',
                'filter'=>['enabled'=>Yii::t('app','Enabled'),'disabled'=>Yii::t('app','Disabled')],
                'value'=>function ($model, $key, $index, $column){
                    return Yii::t('app', ucfirst($model->{$column->attribute}) );
                },
            ],
            ['class' => 'app\components\grid\ActionColumn'],
        ],
    ]); $grid->end(); ?>

</div>
