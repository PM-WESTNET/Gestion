<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\ProfileClass $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile Classes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-class-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->profile_class_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->profile_class_id], [
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
            'profile_class_id',
            'name',
            'hint',
            [
                'label'=>$model->getAttributeLabel('data_type'),
                'value'=>Yii::t('app',  ucfirst($model->data_type))
            ],
            [
                'label'=>$model->getAttributeLabel('status'),
                'value'=>Yii::t('app',  ucfirst($model->status))
            ],
            'data_min',
            'data_max',
            'pattern',
            'order',
            'multiple:boolean',
        ],
    ]) ?>

</div>
