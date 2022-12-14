<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Action */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Action',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Actions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->action_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="action-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
