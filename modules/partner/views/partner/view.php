<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\partner\models\Partner */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('partner', 'Partners'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-view">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->partner_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->partner_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'label' => Yii::t('accounting', 'Account'),
                'value' => $model->account->name
            ]
        ],
    ]) ?>

</div>
