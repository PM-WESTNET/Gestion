<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\automaticdebit\models\Bank;
use app\modules\automaticdebit\models\AutomaticDebit;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\automaticdebit\models\AutomaticDebitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Automatic Debits');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="automatic-debit-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Automatic Debit'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'customer_id',
                'value' => function ($model) {
                    return $model->customer->fullName . ' ('. $model->customer->code . ')';
                },
                'filter' => $this->render('../../../sale/views/customer/_find-with-autocomplete', ['form' => null, 'model' => $searchModel, 'attribute' => 'customer_id', 'label' => Yii::t('app','Customer')])
            ],
            [
                'attribute' => 'company_name',
                'value' => function($model) {
                    return $model->customer->company->name;
                },
                'label' => Yii::t('app', 'Company'),
            ],
            [
                'attribute' => 'bank_id',
                'contentOptions' => ['style' => 'min-width:130px; white-space: normal;'],
                'value' => function ($model) {
                    return $model->bank->name;
                },
                'filter' => ArrayHelper::map(Bank::find()->all(), 'bank_id', 'name')
            ],
            'cbu',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->getStatusLabel();
                },
                'filter' => [
                    AutomaticDebit::ENABLED_STATUS => Yii::t('app','Enabled'),
                    AutomaticDebit::DISABLED_STATUS => Yii::t('app','Disabled')
                ]
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return date('d-m-Y', $model->created_at);
                },
                'label' => Yii::t('app', 'Created at'),
            ],
            [
                'attribute' => 'user_id',
                'value' => function($model) {
                    //var_dump($model);
                    //die;
                    if ($model->user){
                        return $model->user->username;      
                    }
                },
                'filter' => Select2::widget([
                    'name' => 'AutomaticDebitSearch[user_id]',
                    'data' => ArrayHelper::map($userData, 'id', 'username'),
                    'options' => ['placeholder' => Yii::t('app', 'Select an option')],
                    'pluginOptions' => ['allowClear' => true]
                ]),
                'label' => Yii::t('app', 'Created by'),

            ],
            ['class' => 'app\components\grid\ActionColumn'],
        ],
    ]); ?>
</div>
