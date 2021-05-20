<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use webvimark\modules\UserManagement\models\User;
use app\modules\firstdata\models\FirstdataCompanyConfig;

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
                'attribute' => 'user_id', 
                'value' => function($model) {
                    if ($model->user){
                        return $model->user->username;
                    }
                },
                'filter' => Select2::widget([
                    'name' => 'FirstdataAutomaticDebitSearch[user_id]',
                    'data' => ArrayHelper::map(User::find()->all(), 'id', 'username'),
                    'options' => ['placeholder' => Yii::t('app', 'Select an option')],
                    'pluginOptions' => ['allowClear' => true]
                ])

            ],
            [
                'attribute' => 'adhered_by',
                'value' => function($model) {
                    if($model->adhered_by == "administration")
                        return Yii::t('app', "Administration");

                    else if($model->adhered_by == "sales")
                        return Yii::t('app', "Sales");

                    return null;
                }
            ],
            'created_at:date',

            [
                'class' => 'app\components\grid\ActionColumn'
            ],
        ],
    ]); ?>
</div>
