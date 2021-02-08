<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'log_id',
            'route',
            [
                'attribute' => 'username',
                'value' => function($model){
                    $user = \webvimark\modules\UserManagement\models\User::findOne($model->user_id);
                    return $user->username;
                },
            ],
            [
                'attribute' => 'datetime',
                'format' => 'datetime',
                'filter' => false
            ],
            'model',
            // 'model_id',
            // 'data:ntext',

            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

</div>
