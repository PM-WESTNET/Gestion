<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataExport */

$this->title = Yii::t('app', 'Update Firstdata Export: ' . $model->firstdata_export_id, [
    'nameAttribute' => '' . $model->firstdata_export_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Firstdata Exports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->firstdata_export_id, 'url' => ['view', 'id' => $model->firstdata_export_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="firstdata-export-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
