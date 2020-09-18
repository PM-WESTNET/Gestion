<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\NodeChangeProcess */

$this->title = Yii::t('app', 'Update Node Change Process: ' . $model->node_change_process_id, [
    'nameAttribute' => '' . $model->node_change_process_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Node Change Processes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->node_change_process_id, 'url' => ['view', 'id' => $model->node_change_process_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="node-change-process-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
