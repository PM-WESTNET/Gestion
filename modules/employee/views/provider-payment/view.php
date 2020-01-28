<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\employee\models\EmployeePayment;

/* @var $this yii\web\View */
/* @var $model app\modules\employee\models\EmployeePayment */

$this->title = Yii::t('app', 'Payment to') . $model->employee->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employee Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-payment-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?php
            if($model->canClose()) {
                echo Html::a("<span class='glyphicon glyphicon-repeat'></span> " . Yii::t('app', 'Close'), ['close', 'id' => $model->employee_payment_id], ['class' => 'btn btn-warning']);
                if ($model->getUpdatable()) {
                    echo Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->employee_payment_id], ['class' => 'btn btn-primary']);
                }
            }

            if ($model->status == EmployeePayment::STATUS_CLOSED){
                echo Html::a("<span class='glyphicon glyphicon-indent-right'></span> " .Yii::t('app', 'Apply to bill'), ['apply', 'employee_payment_id' => $model->employee_payment_id], ['class' => 'btn btn-warning']);
            }

            if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->employee_payment_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?php
    $attributes = [];
    if (Yii::$app->params['companies']['enabled']) {
        $attributes[] = [
            'label' => Yii::t('app', 'Company'),
            'value' => $model->company_id ? $model->company->name: ''
        ];
        $attributes[] = [
            'label' => Yii::t('partner', 'Partner Distribution Model'),
            'value' => $model->partnerDistributionModel ? $model->partnerDistributionModel->name: ''
        ];
    }

    $attributes = array_merge($attributes, [
        [
            'value' => $model->employee->name,
            'label' => Yii::t('app', 'Employee')
        ],

        'date:date',
        [
            'attribute' => 'timestamp',
            'value' => function($model) {
                return $model->timestamp ? (new \DateTime('now'))->setTimestamp($model->timestamp)->format('d-m-Y') : '';
            }
        ],
        'amount:currency',
        'description',
        [
            'attribute' => 'status',
            'value' => function($model) {
                return Yii::t('app', $model->status);
            }
        ],
    ]);

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes
    ]) ?>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app', 'Items') ?></h3>
        </div>
        <div class="panel-body">

            <?= GridView::widget([
                'id'=>'grid',
                'dataProvider' => new ActiveDataProvider([
                    'query' => $model->getEmployeePaymentItems()
                ]),
                'options' => ['class' => 'table-responsive'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => Yii::t('app', 'Description'),
                        'value'=> function($model, $key){
                            return $model->description;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Number'),
                        'value'=> function($model, $key){
                            return $model->number;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Payment Method'),
                        'value'=> function($model, $key){
                            return ($model->paymentMethod!==null ? $model->paymentMethod->name : "") .
                            ($model->paycheck ? " - " . $model->paycheck->moneyBox->name . ": " . $model->paycheck->number :  "" ) .
                            ($model->moneyBoxAccount ? " - " . $model->moneyBoxAccount->number :  "" )
                            ;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Amount'),
                        'value'=> function($model, $key){
                            return Yii::$app->formatter->asCurrency($model->amount);
                        }
                    ],
                ],
                'options'=>[
                    'style'=>'margin-top:10px;'
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app', 'Bills') ?></h3>
        </div>
        <div class="panel-body">

            <?= GridView::widget([
                'id'=>'grid',
                'dataProvider' => new ActiveDataProvider([
                    'query' => $model->getEmployeeBillHasEmployeePayments(),
                ]),
                'options' => ['class' => 'table-responsive'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => Yii::t('app', 'Bill Type'),
                        'value'=> function($model, $key){
                            return ($model->employeeBill!==null ? $model->employeeBill->billType->name: "" ) ;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Date'),
                        'value'=> function($model, $key){
                            return ($model->employeeBill!==null ? Yii::$app->formatter->asDate($model->employeeBill->date) : "" );
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Amount'),
                        'value'=> function($model, $key){
                            return ($model->employeeBill!==null ? Yii::$app->formatter->asCurrency($model->employeeBill->total) : "");
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Balance'),
                        'value'=> function($model, $key){
                            return ($model->employeeBill!==null ? Yii::$app->formatter->asCurrency($model->employeeBill->balance) : "");
                        }
                    ]
                ],
                'options'=>[
                    'style'=>'margin-top:10px;'
                ]
            ]);
            ?>
        </div>
    </div>

</div>
