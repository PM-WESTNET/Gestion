<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\ProgrammaticChangePlan */

$this->title = Yii::t('app', 'Create Programmatic Change Plan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Programmatic Change Plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programmatic-change-plan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'planes' => $planes,
        'customer' => $customer
    ]) ?>

</div>
