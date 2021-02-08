<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\media\models\types\search\MediaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Media');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="image-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Media'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'media_id',
            'title',
            'description',
            'name',
            'base_url:url',
            // 'relative_url:url',
            // 'type',
            // 'mime',
            // 'size',
            // 'width',
            // 'height',
            // 'extension',
            // 'create_date',
            // 'create_time',
            // 'create_timestamp:datetime',
            // 'status',

            ['class' => 'app\components\ActionColumn'],
        ],
    ]); ?>

</div>
