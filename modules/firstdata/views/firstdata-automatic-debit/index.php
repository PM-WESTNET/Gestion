<?php

use app\modules\firstdata\models\FirstdataCompanyConfig;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\firstdata\models\search\FirstdataAutomaticDebitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Firstdata Automatic Debits');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firstdata-automatic-debit-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Firstdata Automatic Debit'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'customer_id',
                'value' => function($model) {
                    return $model->customer->fullName . ' ('. $model->customer->code . ')';
                },
                'filter' => $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['model' => $searchModel, 'attribute' => 'customer_id'])
            ],
            [
                'attribute' => 'company_config_id', 
                'value' => function($model) {
                    return $model->companyConfig->company->name;
                },
                'filter' => ArrayHelper::map(FirstdataCompanyConfig::find()->all(), 'firstdata_company_config_id', 'company.name')

            ],

            [
                'class' => 'app\components\grid\ActionColumn'
            ],
        ],
    ]); ?>
</div>
