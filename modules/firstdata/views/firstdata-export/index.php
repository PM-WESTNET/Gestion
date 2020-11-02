<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\firstdata\models\search\FirstdataExportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Firstdata Exports');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firstdata-export-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Firstdata Export'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'firstdata_export_id',
            'created_at:timestamp',
            'file_url',
            [
                'attribute' => 'firstdata_config_id',
                'value' => function($model) {
                    return $model->firstdataConfig->company->name;
                }
            ],

            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>
</div>
