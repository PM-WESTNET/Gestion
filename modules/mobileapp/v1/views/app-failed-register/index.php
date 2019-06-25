<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\mobileapp\v1\models\AppFailedRegister;
use kartik\select2\Select2;
use app\modules\sale\models\Customer;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\mobileapp\v1\models\AppFailedRegisterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Mobile App failed registers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-failed-register-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'customer_code',
                'value' => function ($model) {
                    if($model->customer_code) {
                        $customer = Customer::findOne(['code' => $model->customer_code]);
                        return Html::a($model->customer_code .' - '. $customer->fullName, ['/sale/customer/view', 'id' => $customer->customer_id]);
                    }
                    return '';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'document_number',
                'value' => function($model){
                    return $model->document_type . ': '. $model->document_number;
                },

            ],
            'email:email',
            'phone',
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    return Yii::t('app', $model->type);
                },
                'filter' => Select2::widget([
                                'name' => 'AppFailedRegisterSearch[type]',
                                'data' => AppFailedRegister::getTypesForSelect(),
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Select'),
                                ],
                            ])
            ],
            [
                'attribute' => 'text',
                'value' => function ($model) {
                    return $model->text;
                },
                'filter' => \yii\bootstrap\Html::textInput('AppFailedRegisterSearch[text])', $searchModel->text, ['class' => 'form-control']),
                'format' => 'text'
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'filter' => \kartik\date\DatePicker::widget([
                    'name' => 'AppFailedRegisterSearch[created_at]',
                    'pickerButton' => false,
                    'pluginOptions' => [
                        'format' => 'dd-m-yyyy'
                    ]
                ])
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'buttons' => [
                    'status' => function($url, $model){
                        return Html::a('<span class="glyphicon glyphicon-ok"></span>', ['close', 'id' => $model->app_failed_register_id], ['class' => 'btn btn-success']);
                    }
                ],
                'template' => '{status}'
            ],
        ],
    ]); ?>

</div>
