<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\mobileapp\v1\models\AppFailedRegister */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'App Failed Register',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'App Failed Registers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->app_failed_register_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="app-failed-register-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
