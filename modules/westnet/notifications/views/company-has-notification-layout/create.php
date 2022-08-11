<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\CompanyHasNotificationLayout */

$this->title = 'Create Company Has Notification Layout';
$this->params['breadcrumbs'][] = ['label' => 'Company Has Notification Layouts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-has-notification-layout-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
