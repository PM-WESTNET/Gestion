<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\mobileapp\v1\models\AppFailedRegisterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'App Failed Registers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-failed-register-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'name',
                'filter' => \yii\bootstrap\Html::textInput('AppFailedRegisterSearch[name])', $searchModel->name, ['class' => 'form-control']),
            ],
            [
                'attribute' => 'document_number',
                'value' => function($model){
                    return $model->document_type . ': '. $model->document_number;
                },

            ],
            'email:email',
            'phone',
            // 'status',

            [
                'class' => 'app\components\grid\ActionColumn',
                'buttons' => [
                    'status' => function($url, $model){
                        return Html::a('<span class="glyphicon glyphicon-ok"></span>', ['close', 'id' => $model->app_failed_register_id], ['class' => 'btn btn-success']);
                    }
                ],
                'template' => '{status}'
            ],
        ],
    ]); ?>

</div>
