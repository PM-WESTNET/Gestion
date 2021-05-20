<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\widgets\DetailView;
use yii\data\ActiveDataProvider;
use app\components\grid\ActionColumn;
use app\modules\sale\models\Customer;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataImport */

$this->title = Yii::t('app','Firstdata Import') . ': ' .$model->firstdata_import_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Firstdata Imports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firstdata-import-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php 
            if ($model->status === 'draft'){
                echo Html::a('<span class="glyphicon glyphicon-close"></span>'. Yii::t('app', 'Close'), ['close-payments', 'id' => $model->firstdata_import_id], ['class' => 'btn btn-warning']);
            }
        ?>
        
        <?php if ($model->status === "draft"):?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->firstdata_import_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'firstdata_import_id',
            [
                'attribute' => 'firstdata_config_id',
                'value' => function($model) {
                    return $model->firstdataConfig->company->name;
                }
            ],
            'presentation_date',
            'created_at',
            'status',
            'response_file',
            'observation_file',
        ],
    ]) ?>

    <h3><?= Yii::t('app', 'Payments')?></h3>

    <?php

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $search,
            'columns' =>  [
                ['class' => SerialColumn::class],

                [
                    'attribute' => 'customer_code'
                ],
                [
                    'attribute' => 'customer_id',
                    'value' => function($model) {
                        if ($model->customer) {
                            return $model->customer->fullName;
                        } else {
                            $customer = Customer::findOne(['code' => $model->customer_code]);

                            if ($customer) {
                                return $customer->fullName;
                            }
                        }
                    },
                    'filter' => $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['model' => $search, 'attribute' => 'customer_id'])
                ],
                'amount:currency',
                [
                    'attribute' => 'status',
                    'filter' => [
                        'pending' => Yii::t('app', 'Pending'),
                        'success' => Yii::t('app', 'Success'),
                        'error' => Yii::t('app', 'Error'),
                    ]
                ],
                'error',

                [
                    'class' => ActionColumn::class,
                    'buttons' => [
                        'view' => function ($url, $model) {
                            if($model->payment) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open">', ['/checkout/payment/view', 'id' => $model->payment_id], ['class' => 'btn btn-default', 'target' => '_blank']);
                            }
                        }
                    ],
                    'template' => '{view}'
                ]
            ]
        ])
    
    ?>

</div>
