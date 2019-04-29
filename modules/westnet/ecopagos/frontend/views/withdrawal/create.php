<?php

use app\modules\westnet\ecopagos\EcopagosModule;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Withdrawal */

$this->title = EcopagosModule::t('app', 'Execute withdrawal');
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Withdrawals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdrawal-create container">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
