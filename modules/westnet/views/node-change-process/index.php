<?php

use app\components\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\models\search\NodeChangeProcessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Node Change Processes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="node-change-process-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Node Change Process'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'node_change_process_id',
            'created_at',
            'ended_at',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return Yii::t('app', $model->status);
                }
            ],
            [
                'attribute' => 'node_id',
                'value' => function($model) {
                    return $model->node->name;
                }
            ],
            [
                'attribute' => 'creator_user_id',
                'value' => function($model) {
                    return $model->creatorUser->username;
                }
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view}{delete}'
            ],
        ],
    ]); ?>
</div>
