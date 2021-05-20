<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataImport */

$this->title = Yii::t('app', 'Update Firstdata Import: ' . $model->firstdata_import_id, [
    'nameAttribute' => '' . $model->firstdata_import_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Firstdata Imports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->firstdata_import_id, 'url' => ['view', 'id' => $model->firstdata_import_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="firstdata-import-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'companies_config' => $companies_config,
    ]) ?>

</div>
