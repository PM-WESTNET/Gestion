<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\instructive\models\InstructiveCategory */

$this->title = Yii::t('app','Instructive Category') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Instructive Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="instructive-category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app','Update'), ['update', 'id' => $model->instructive_category_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a(Yii::t('app','Delete'), ['delete', 'id' => $model->instructive_category_id], [
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
            'instructive_category_id',
            'name',
            [
                'attribute' => 'status',
                'value' => function ($model){
                    return $model->getStatusLabel();
                }
            ],
            [
                'label' => 'Roles',
                'value' => function ($model) {
                    $roles = '';
                    foreach ($model->roles as $role) {
                        $roles .= $role->description. ', ';
                    }

                    return rtrim($roles, ', ');
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
