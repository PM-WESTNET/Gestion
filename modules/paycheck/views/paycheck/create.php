<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\paycheck\models\Paycheck */

if (!$embed) {
    $this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('paycheck','Paycheck')]);
    $this->params['breadcrumbs'][] = ['label' => Yii::t('paycheck', 'Paychecks'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
        <div class="paycheck-create">

            <h1><?= Html::encode($this->title)  ?></h1>

            <?= $this->render('_form', [
                'model' => $model,
                'embed' => $embed,
                'for_payment' => $for_payment,
                'from_thrid_party' => $from_thrid_party
            ]);
            ?>

        </div>
    </div>
</div>
