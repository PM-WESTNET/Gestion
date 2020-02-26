<?php

use app\components\helpers\UserA;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\employee\models\search\EmployeeCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Employee Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-category-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= UserA::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create Employee Category'), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
                'attribute' => 'statusLabel',
                'label' => Yii::t('app', 'Status')
            ],

            ['class' => 'app\components\grid\ActionColumn'],
        ],
    ]); ?>
</div>
