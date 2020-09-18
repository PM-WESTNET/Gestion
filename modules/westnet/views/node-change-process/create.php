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

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
