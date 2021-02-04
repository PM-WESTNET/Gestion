<?php

use yii\helpers\Html;
use app\modules\westnet\ecopagos\EcopagosModule;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Cashier */

$this->title = EcopagosModule::t('app', 'Create Cashier') . ' | ' . $ecopago->name;
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Ecopagos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $ecopago->name, 'url' => ['ecopago/view', 'id' => $ecopago->ecopago_id]];
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Cashiers'), 'url' => ['list-by-ecopago', 'ecopago_id' => $ecopago->ecopago_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashier-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
