<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\HourRange */

$this->title = Yii::t('app', 'Update Hour Range'). ' ' . $model->hour_range_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Hour Ranges'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->hour_range_id, 'url' => ['view', 'id' => $model->hour_range_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="hour-range-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
