<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\CompanyHasNotificationLayout */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Company Has Notification Layouts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-has-notification-layout-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'layout_path',
            'company_id',
            'is_enabled',
        ],
    ]) ?>

</div>
