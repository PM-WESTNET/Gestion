<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\Bank */

$this->title = Yii::t('app', 'Update {modelClass}', ['modelClass' => Yii::t('app','Bank')]).
    ': '. $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banks for Automatic Debit'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->bank_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="bank-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
