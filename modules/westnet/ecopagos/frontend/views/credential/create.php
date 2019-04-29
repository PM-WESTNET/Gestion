<?php

use app\modules\westnet\ecopagos\EcopagosModule;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Credential */

$this->title = EcopagosModule::t('app', 'Create Credential');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Credentials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="credential-create container">

    <!-- Title and comment -->
    <h1 class="position-relative to-white font-white" style="z-index: 11;">
        <?= Html::encode($this->title) ?>        

        <small class="to-white font-white">
            <?= EcopagosModule::t('app', 'Here you, as a cashier, can register a new ask for credential re-printing for a specific customer.'); ?>
        </small>
    </h1>
    <!-- end Title and comment -->

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
