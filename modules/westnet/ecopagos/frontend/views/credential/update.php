<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Credential */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Credential',
]) . ' ' . $model->credential_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Credentials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->credential_id, 'url' => ['view', 'id' => $model->credential_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="credential-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
