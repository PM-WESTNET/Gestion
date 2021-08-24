<?php

use yii\helpers\Html;
use yii\grid\SerialColumn;
use kartik\date\DatePicker;

$this->title = Yii::t('app', 'Customers per discount');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-12">
        <h1 class="profile-link">Clientes Por Descuento: <?= '"'.$model->name.'"' ?></h1>
        <?php echo \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $discountSearch,
            'columns' => [
                ['class' => SerialColumn::class],
                [
                    'attribute' => 'name',
                    'format' => 'html',
                    'label' => 'Cliente',
                    'value' => function($model){
                        return Html::a($model->lastname . ' ' . $model->name . ' (' .$model->code . ')', 
                                    ['/sale/customer/view', 'id' => $model->customer_id], 
                                    ['class' => 'profile-link']);
                    }
                ],
                [
                    'attribute' => 'status',
                    'format' => 'html',
                    'value' => function ($model) {
                        $labelType = ($model->status == "enabled")? "success" : "danger";
                        return "<span class='label label-$labelType'>$model->status</span>";
                    },
                    'filter'=>['enabled'=>Yii::t('app','Enabled'), 'disabled'=>Yii::t('app','Disabled')]
                ],
                [
                    'attribute'=>'customer_has_discount_from_date',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->from_date . ' - ' . $model->to_date;
                    },
                    'label' => Yii::t('app','Date'),
                    'filter'=>DatePicker::widget([
                        'model' => $discountSearch,
                        'attribute' => 'customer_has_discount_from_date',
                        'value' => '2014-01-01',
                        'type' => DatePicker::TYPE_RANGE,
                        'attribute2' => 'customer_has_discount_to_date',
                        'value2' => '2016-01-01',
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd'
                        ]
                    ]),  
                ],
                [
                    'class' => 'app\components\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open updateItem btn btn-primary"></span>',
                                [
                                    '/sale/customer-has-discount/index',
                                    'customer_id' =>  $model->customer_id
                                ]
                            );
                        }
                    ]
                ],
            ]
        ]) ?>
    </div>
</div>