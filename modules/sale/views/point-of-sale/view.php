<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\PointOfSale */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Points of Sale'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="point-of-sale-view">

    <?php 
    //Mensaje
    if(!Yii::$app->params['companies']['enabled'])
        echo \yii\bootstrap\Alert::widget([
        'options' => [
            'class' => 'alert-warning',
        ],
          'body' => Yii::t('app', 'Only visible to superadmin.'),
     ]); ?>
    
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->point_of_sale_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " .Yii::t('app', 'Delete'), ['delete', 'id' => $model->point_of_sale_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'point_of_sale_id',
            [
                'attribute' => 'company_id',
                'label' => $model->getAttributeLabel('company'),
                'value' => Html::a($model->company->name, ['company/view', 'id' => $model->company_id]),
                'format' => ['html']
            ],
            'name',
            'number',
            [
                'attribute' => 'status',
                'value' => Yii::t('app', ucfirst($model->status))
            ],
            'description',
            'default:boolean',
            [
                'attribute' => 'electronic_billing',
                'value' => function($model){
                    if($model->electronic_billing == 1){
                        return Yii::t('app', 'Yes');
                    }
                    return Yii::t('app', 'No');
                }
            ]
        ],
    ]) ?>

</div>
