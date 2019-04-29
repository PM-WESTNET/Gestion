<?php

use yii\helpers\Html;
use app\modules\westnet\ecopagos\EcopagosModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Cashier */

$this->title = EcopagosModule::t('app', "Change my cashier's password");
$this->params['breadcrumbs'][] = Yii::t('app', 'Change password');
?>
<div class="cashier-update container">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
