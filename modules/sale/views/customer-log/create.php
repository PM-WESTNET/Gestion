<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerLog */

$this->title = 'Create Customer Log';
$this->params['breadcrumbs'][] = ['label' => 'Customer Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
