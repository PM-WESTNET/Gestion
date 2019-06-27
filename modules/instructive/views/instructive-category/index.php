<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\instructive\models\InstructiveCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Instructive Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="instructive-category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app','Create Instructive Category'),
        ['create'], 
        ['class' => 'btn btn-success']) 
        ;?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
                'attribute' => 'status',
                'value' => function ($model){
                    return $model->getStatusLabel();
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
