<?php

use yii\helpers\Html;
use yii\grid\SerialColumn;

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
                    'attribute' => 'from_date',
                    'format' => 'raw',
                    'label' => 'Desde',
                ],
                [
                    'attribute' => 'to_date',
                    'format' => 'raw',
                    'label' => 'Hasta',
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