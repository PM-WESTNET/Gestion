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
                    return $model->document_type ?  $model->document_type. ': '. $model->document_number : $model->document_number;
                },

            ],
            [
                'label' => Yii::t('app','Emails'),
                'value' => function ($model) {
                    $emails = '';
                    if ($model->email) {
                        $emails .= '<h5>'.Yii::t('app', 'Email').': '.$model->email.'</h5>';
                    }

                    if ($model->email2) {
                        $emails .= '<h5>'.Yii::t('app', 'Secondary Email').': '.$model->email2.'</h5>';
                    }

                    return $emails;
                },
                'format' => 'raw'
            ],
            [
                'label' => Yii::t('app','Phones'),
                'value' => function ($model) {
                    $phones = '';
                    if ($model->phone) {
                        $phones .= '<h5>'.Yii::t('app', 'Phone').': '.$model->phone.'</h5>';
                    }

                    if ($model->phone2) {
                        $phones .= '<h5>'.Yii::t('app', 'Second Phone').': '.$model->phone2.'</h5>';
                    }

                    if ($model->phone3) {
                        $phones .= '<h5>'.Yii::t('app', 'Third Phone').': '.$model->phone3.'</h5>';
                    }


                    if ($model->phone4) {
                        $phones .= '<h5>'.Yii::t('app', 'Cellphone 4').': '.$model->phone4.'</h5>';
                    }



                    return $phones;
                },
                'format' => 'raw'
            ],
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
