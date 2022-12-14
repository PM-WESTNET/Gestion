<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\backup\models\Backup */

$this->title = Yii::t('app', 'Update Backup: ' . $model->backup_id, [
    'nameAttribute' => '' . $model->backup_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Backups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->backup_id, 'url' => ['view', 'id' => $model->backup_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="backup-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
