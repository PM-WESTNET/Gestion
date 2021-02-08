<?php

use app\modules\paycheck\models\Paycheck;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\employee\models\EmployeePayment;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\employee\models\search\EmployeePaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Employee Payments') . ( $employee!==null ?  " - " . $employee->name : "" ) ;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-payment-index">
    
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                    'modelClass' => Yii::t('app','Employee Payment'),
                ]), ['employee-payment/create', 'employee'=>($employee ? $employee->employee_id : null )], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
        $item = '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters');

        echo \yii\bootstrap\Collapse::widget([
            'items' => [
                [
                    'label' => $item,
                    'content' => $this->render('_employee-payment-filters', ['model' => $searchModel]),
                    'encode' => false,
                ],
            ],
            'options' => [
                'class' => 'hidden-print'
            ]
        ]);
    ?>

    <?php

        $columns[] = ['class' => 'yii\grid\SerialColumn'];
        if ($employee===null) {
            $columns[] = [
                'header' => Yii::t('app','Employee'),
                'value' => function($model){  return $model['employee']; },
            ];
        }

        $columns[] = [
            'label' => Yii::t('app', 'Payment Method'),
            'value' => function($model) { return ($model['payment_method'] ? $model['payment_method'] : '' ); }
        ];
        $columns[] = [
            'label' => Yii::t('app', 'Date'),
            'value' => function($model) { return ($model['date'] ? Yii::$app->formatter->asDate( $model['date'] ) : '' ); }
        ];
        $columns[] = [
            'attribute' => 'status',
            'value' => function($model) { return Yii::t('app', $model['status']); }
        ];
        $columns[] = [
            'label' => Yii::t('app', 'Amount'),
            'value' => function($model) { return ($model['amount'] ? Yii::$app->formatter->asCurrency( $model['amount'] ) : '' ); }
        ];
        $columns[] = [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>'{view} {update} {delete}',
                'buttons'=>[
                    'view' => function ($url, $model, $key) {
                        return '<a href="'.Url::toRoute(['employee-payment/view', 'id'=>$model['employee_payment_id']]).'" class="btn btn-view"><span class="glyphicon glyphicon-eye-open"></span></a>';

                    },
                    'update' => function ($url, $model, $key) {
                        $employee_payment = EmployeePayment::findOne($model['employee_payment_id']);
                        if ($employee_payment->getUpdatable()) {
                            return ($model['status'] != 'created' ? '' : '<a href="' . Url::toRoute(['employee-payment/update', 'id' => $model['employee_payment_id']]) . '" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>');
                        }

                    },
                    'delete' => function ($url, $model, $key) {
                        $payment = EmployeePayment::findOne($model['employee_payment_id']);
                        if($payment) {
                            if($payment->getDeletable()) {
                                return '<a href="' . Url::toRoute(['employee-payment/delete', 'id' => $model['employee_payment_id']]) .
                                    '" title="' . Yii::t('app', 'Delete') . '" data-confirm="' . Yii::t('yii', 'Are you sure you want to delete this item?') . '" data-method="post" data-pjax="0" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                            }
                        }
                    },
                ]
            ];

    ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'columns' => $columns
    ]); ?>

</div>
