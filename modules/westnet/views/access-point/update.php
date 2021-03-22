<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\AccessPoint */

$this->title = Yii::t('app', 'Update Access Point: ' . $model->name, [
    'nameAttribute' => '' . $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Access Points'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->access_point_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="access-point-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'nodes' => $nodes
    ]) ?>

</div>
