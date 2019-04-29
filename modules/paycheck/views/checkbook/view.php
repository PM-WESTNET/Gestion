<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\paycheck\models\Checkbook */

$this->title = Yii::t('paycheck', 'Checkbook') . " " . $model->moneyBoxAccount->moneyBox->name . " - " . $model->moneyBoxAccount->number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('paycheck', 'Checkbooks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="checkbook-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->checkbook_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->checkbook_id], [
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
            [
                'label' => Yii::t('paycheck', 'Money Box'),
                'attribute' => 'moneyBoxAccount.moneyBox.name'
            ],
            [
                'label' => Yii::t('paycheck', 'Money Box Account'),
                'attribute' => 'moneyBoxAccount.number'
            ],
            'start_number',
            'end_number',
            'last_used',
            'enabled:boolean',
        ],
    ]) ?>

</div>
