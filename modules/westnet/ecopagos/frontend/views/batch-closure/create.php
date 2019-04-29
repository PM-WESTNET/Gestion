<?php

use yii\helpers\Html;
use app\modules\westnet\ecopagos\EcopagosModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\BatchClosure */

$this->title = EcopagosModule::t('app', 'Execute batch closure');
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Batch Closures'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container bg-white batch-closure-create">

    <h1 class="position-relative to-white font-white" style="z-index: 11;">
        <?= Html::encode($this->title) ?>
        <small class="clearfix to-white font-white">
            <?= EcopagosModule::t('app', "A batch closure is an operation for making a collection of money from an Ecopago branch. It is necessary a collector's authentication due to security reasons."); ?>
        </small>
    </h1>

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
