<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\models\search\AccessPointSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Access Points');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="access-point-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Access Point'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'access_point_id',
            'name',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->statusLabel;
                },
                'filter' => [
                    'enabled' => Yii::t('app', 'Enabled'),
                    'disabled' => Yii::t('app', 'Disabled'),
                ]
            ],
            [
                'attribute' => 'node_id',
                'value' => function($model) {
                    $ret = 'N/A';
                    if($model->node_id) {
                        if(!empty($model->node->name)) {
                            $ret = $model->node->name;
                        }
                    }
                    return $ret;
                },
                'filter' => Select2::widget([
                    'name' => 'AccessPointSearch[node_id]',
                    'data' => $nodes,
                    'pluginOptions' => ['allowClear' => true],
                    'options' => ['prompt' => '']
                ])
            ],

            ['class' => 'app\components\grid\ActionColumn'],
        ],
    ]); ?>
</div>
