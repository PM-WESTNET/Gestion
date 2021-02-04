<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\IntegratechSmsFilter */

$this->title = $model->integratech_sms_filter_id;
$this->params['breadcrumbs'][] = ['label' => \app\modules\westnet\notifications\NotificationsModule::t('app','Integratech Sms Filters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="integratech-sms-filter-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app','Update'), ['update', 'id' => $model->integratech_sms_filter_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a(Yii::t('app','Delete'), ['delete', 'id' => $model->integratech_sms_filter_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app','Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'integratech_sms_filter_id',
            'word:ntext',
            'action',
            [
                'attribute' => 'category_id',
                'value' => function ($model){
                    return ($model->category ? $model->category->name: '' );
                }
            ],
            [
                    'attribute' => 'is_created_automaticaly',
                    'value' => function($model){
                        return $model->is_created_automaticaly? Yii::t('app', 'Yes') : Yii::t('app', 'No');
                    }
            ]
        ],
    ]) ?>

</div>
