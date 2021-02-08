<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\mobileapp\v1\models\AppFailedRegister */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'App Failed Registers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-failed-register-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->app_failed_register_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->app_failed_register_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'app_failed_register_id',
            'name',
            'lastname',
            'document_type',
            'document_number',
            'customer_code',
            'email:email',
            'phone',
            'status',
        ],
    ]) ?>

</div>
