<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\partner\models\Partner */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('partner','Partner'),
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('partner', 'Partners'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->partner_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="partner-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
