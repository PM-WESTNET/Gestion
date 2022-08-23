<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\CompanyHasNotificationLayout */

$this->title = 'Update Company Has Notification Layout: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Company Has Notification Layouts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="company-has-notification-layout-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
