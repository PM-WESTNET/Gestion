<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\pagomiscuentas\models\PagomiscuentasLiquidation */

$this->title = Yii::t('app', 'Create Pagomiscuentas Liquidation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pagomiscuentas Liquidations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pagomiscuentas-liquidation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
