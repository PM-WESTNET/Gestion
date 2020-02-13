<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\employee\models\Employee */

$this->title = Yii::t('app', 'Employee').': '. $model->fullName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span> '.Yii::t('app','Account'),
                        ['employee/current-account','id'=>$model->employee_id], ['class'=>'btn btn-default']) ?>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->employee_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->employee_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?php
    $attributes = [
        'employee_id',
        'name',
        'lastname',
        [

            'label'=> Yii::t('app', 'Tax Condition'),
            'attribute' => 'taxCondition.name',
        ],
        [

            'label'=> Yii::t('app', 'Document Type'),
            'attribute' => 'documentType.name',
        ],
        'document_number',
        'birthday',
        [
            'attribute' => 'employee_category_id',
            'value' => function ($model) {
                if ($model->employeeCategory) {
                    return $model->employeeCategory->name;
                }
            }
        ],
        'init_date',
        'finish_date',
        [
            'attribute' => 'address_id',
            'value' => function ($model) {
                return $model->address->fullAddress;
            }
        ],
        'phone',
        [
            'attribute' => 'company_id',
            'value' => function ($model) {
                return $model->company->name;
            }
        ]
    ];

    if (Yii::$app->getModule('accounting') ) {
        $attributes[] = [
            'label' => Yii::t('accounting', 'Account'),
            'attribute'=>'account.name'
        ];
    }

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes
    ]) ?>

</div>
