<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\models\search\AdsPercentagePerCompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Ads Percentage Per Companies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ads-percentage-per-company-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Ads Percentage Per Company'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'percentage_per_company_id',
            'parent_company_id',
            'company_id',
            'percentage',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
