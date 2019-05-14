<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\cobrodigital\models\PaymentCardFile */

$this->title = Yii::t('cobrodigital', 'Import Payment Card File');
$this->params['breadcrumbs'][] = ['label' => Yii::t('cobrodigital', 'Payment Card Files'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-card-file-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
