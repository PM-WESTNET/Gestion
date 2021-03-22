<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\IntegratechSmsFilter */

$this->title = \app\modules\westnet\notifications\NotificationsModule::t('app', 'Create Integratech Sms Filter');
$this->params['breadcrumbs'][] = ['label' => \app\modules\westnet\notifications\NotificationsModule::t('app', 'Integratech Sms Filters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="integratech-sms-filter-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
