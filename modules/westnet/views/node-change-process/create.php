<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\NodeChangeProcess */

$this->title = Yii::t('app', 'Create Node Change Process');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Node Change Processes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="node-change-process-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="panel panel-info">
        <div class="panel-heading">Nota:</div>
        <div class="panel-body">
            El archivo a cargar debe ser un csv y contener los siguientes campos separados por comas en el siguiente orden
            'ip', 'nombre_del_nodo','contract_id',
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
