<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\models\search\IpRankSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Networks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ip-rank-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . 'Create Network', 
        ['create'], 
        ['class' => 'btn btn-success']) 
        ;?>
    </p>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ip_range_id',
            [
                'attribute' => 'ip_start',
                'value' => function($model) {
                    return long2ip($model->ip_start);
                }
            ],
            [
                'attribute' => 'ip_end',
                'value' => function($model) {
                    return long2ip($model->ip_end);
                }
            ],
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->statusLabel;
                }
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
