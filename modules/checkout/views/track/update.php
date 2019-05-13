<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\Track */

$this->title = Yii::t('app', 'Update Track: ') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tracks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->track_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="track-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
