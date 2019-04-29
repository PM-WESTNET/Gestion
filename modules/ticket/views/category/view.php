<?php

use app\modules\ticket\TicketModule;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Category */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->category_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->category_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?php
    $external_user = $model->getExternaUser();
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => TicketModule::t('app', 'Parent'),
                'value' => ($model->parent ? $model->parent->name: '' )
            ],
            'name',
            [
                'label' => TicketModule::t('app', 'Notify'),
                'value' => Yii::t('app', ($model->notify ?  'Yes' : 'No' ))
            ],
            [
                'attribute' => 'schema_id',
                'value' => function ($model) {
                    return $model->schema->name;
                }
            ],
            [
                'label' => TicketModule::t('app', 'External User'),
                'value' => ($external_user ? $external_user->nombre : '' )
            ],
            [
                'label' => Yii::t('app', 'Responsible user'),
                'value' => function ($model) {
                    return $model->responsible_user_id ? $model->responsibleUser->username : '';
                }
            ],
            'description:ntext',
            'slug',
        ],
    ]) ?>

</div>
