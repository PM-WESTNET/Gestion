<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Discounts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="discount-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
        'modelClass' => Yii::t('app', 'Discount'),
        ]),
            ['create'],
            ['class' => 'btn btn-success'])
            ;?>
        </p>
    </div>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            [
                'attribute'=>'type',
                'value'=>function($model){
                    return Yii::t('app',  ucfirst($model->type));
                }
            ],
            [
                'attribute'=>'refrenced',
                'label' => Yii::t('app', 'Referenced' ),
                'value' => function($model){
                    return Yii::t('app',  ($model->referenced ? 'Yes' : 'No' ));
                }
            ],
            'persistent:boolean',
            'value',
            'from_date:date',
            'to_date:date',
            'periods',
            [
                'attribute'=>'apply_to',
                'value'=>function($model){
                    return Yii::t('app',  ucfirst($model->apply_to));
                }
            ],
            [
                'attribute'=>'value_from',
                'value'=>function($model){
                    return Yii::t('app',  ucfirst($model->value_from));
                }
            ],
            [
                'label' => Yii::t('app', 'Product'),
                'format' => 'html',
                'value' => function ($model) {
                    // $productName = 
                    $ret = '';
                    if ($model->value_from == \app\modules\sale\models\Discount::VALUE_FROM_PRODUCT) {
                        if (isset($model->product)) { // check if product exist
                            $ret = $model->product->name;
                        } else {
                            // $ret = "No tiene producto asociado*";
                            $ret = "<span class='label label-danger'>No tiene producto asociado</span>";
                        }
                    } else {
                        $ret = Yii::t('app', 'No apply');
                    }
                    return $ret;
                }
            ],
            [
                'attribute' => 'status',
                'label' => 'Estado del Descuento',
                'format' => 'html',
                'value' => function ($model) {
                    $labelType = ($model->status == "enabled")? "success" : "danger";
                    return "<span class='label label-$labelType'>$model->status</span>";
                },
                'filter'=>['enabled'=>Yii::t('app','Enabled'), 'disabled'=>Yii::t('app','Disabled')],
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
