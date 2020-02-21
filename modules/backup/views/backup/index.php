<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\backup\models\BackupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Backups');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backup-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'backup_id',
            [
                'attribute' => 'init_timestamp',
                'filter' => \kartik\widgets\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'init_timestamp',
                    'value' => $searchModel->init_timestamp,
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd-mm-yyyy'
                    ]
                ])
            ],
            [
                'attribute' => 'finish_timestamp',
                'filter' => \kartik\widgets\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'finish_timestamp',
                    'value' => $searchModel->finish_timestamp,
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd-mm-yyyy'
                    ]
                ])
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatusLabel();
                },
                'filter' => [
                    'in_process' => Yii::t('app','In Process'),
                    'success' => Yii::t('app', 'Success'),
                    'error' => Yii::t('app', 'Fail'),
                ]
            ],
            'database',

        ],
    ]); ?>
</div>
