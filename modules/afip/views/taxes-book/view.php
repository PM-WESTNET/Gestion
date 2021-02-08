<?php

use app\modules\afip\models\TaxesBook;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\afip\models\TaxesBook */

$this->title = Yii::t('afip', 'Book ' . ucfirst($model->type)) . " - " . Yii::$app->getFormatter()->asDate($model->period, 'M/yyyy') . " - ". Yii::t('afip', 'Number') . " " . $model->number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('afip', 'Book ' . ucfirst($model->type)), 'url' => [$model->type]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="taxes-book-view">
    
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>

            <?php if($model->deletable) {
                echo Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->taxes_book_id], ['class' => 'btn btn-primary']);
                echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->taxes_book_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]);
            }
                if ($model->status==TaxesBook::STATE_DRAFT) {
                    echo Html::a(Yii::t('afip', 'Select Bills'), ['add-'.$model->type.'-bills', 'id' => $model->taxes_book_id], ['class' => 'btn btn-warning']);
                }
            ?>
        </p>        
    </div>


    <?php
    $attributes = [];
    if (Yii::$app->params['companies']['enabled']) {
        $attributes[] = [
            'attribute' => 'company.name',
            'label'     => Yii::t('app', 'Company')
        ];
    }
    $attributes[] = 'number';
    $attributes = array_merge($attributes, [
        [
            'label' => Yii::t('afip', 'Period'),
            'attribute' => function($model){
                return Yii::$app->getFormatter()->asDate($model->period, 'M/yyyy');
            }
        ],
        [
            'attribute' => function($model){
                return Yii::t('accounting', ucfirst($model->status));
            },
            'label' => Yii::t('afip', 'State')
        ]
    ]);

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
    ]) ?>
</div>
