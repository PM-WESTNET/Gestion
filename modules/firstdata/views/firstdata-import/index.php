<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\firstdata\models\search\FirstdataImportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Firstdata Imports');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firstdata-import-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Firstdata Import'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'firstdata_import_id',
            'created_at:datetime',
            'status',
            'response_file',
            //'observation_file',

            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>
</div>
