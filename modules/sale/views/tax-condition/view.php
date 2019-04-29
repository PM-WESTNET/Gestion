<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\TaxCondition */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tax Conditions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-condition-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->tax_condition_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->tax_condition_id], [
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
            'tax_condition_id',
            'name',
            [
                'attribute' => 'billTypes',
                'value'=> $model->getBillTypesNames('<br/>'),
                'format' => ['html']
            ],
            [
                'attribute' => 'billTypesBuy',
                'value'=> $model->getBillTypesBuyNames('<br/>'),
                'format' => ['html']
            ],
            [
                'label' => Yii::t('app', 'Document type required'),
                'value' => $model->getDocumentTypesLabels()
            ],
            'exempt:boolean'
        ],
    ]) ?>

</div>
