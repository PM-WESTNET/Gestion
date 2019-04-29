<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerClass */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customer Classes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-class-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->customer_class_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->customer_class_id], [
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
            'customer_class_id',
            'name',
            'code_ext',
            'is_invoiced:boolean',
            'tolerance_days',
            [
                'attribute'=>'colour', 
                'format'=>'raw', 
                'value'=>"<div style='float:left; margin-right: 10px; width: 24px; height: 24px; background-color: {$model->colour}'> </div> " . $model->colour,
                //'type'=>DetailView::INPUT_COLOR,
                //'inputWidth'=>'100%'
            ],
            'percentage_bill',
            'days_duration',
            'percentage_tolerance_debt',
            'service_enabled:boolean',
            [
                'attribute' => 'status',
                'value' => Yii::t('app', ucfirst($model->status))
            ],            
        ],
    ]) ?>

</div>
