<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\firstdata\models\search\FirstdataCompanyConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Firstdata Company Configs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firstdata-company-config-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'Create Firstdata Company Config'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'firstdata_company_config_id',
            [
                'attribute' => 'company_id',
                'value' => function ($model) {
                    if ($model->company) {
                        return $model->company->name;
                    }
                }
            ],
            'commerce_number',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
