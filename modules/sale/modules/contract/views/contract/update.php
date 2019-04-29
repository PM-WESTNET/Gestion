<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\Contract */

$this->title = Yii::t('app', 'Update Contract') ." - " .  $model->customer->name . " - " . $model->contract_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['/sale/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->customer->name, 'url' => ['/sale/customer/view', 'id'=> $model->customer_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contract Number') .": " . $model->contract_id, 'url' => ['view', 'id' => $model->contract_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

?>
<div class="row">
    <div class="col-sm-12">
        <div class="contract-update">

            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render('_form', [
                'model' => $model,
                'customer'=>$customer,
                'address'=>$address,
                'contractDetailPlan'=>$contractDetailPlan,                
                'same_address' => $same_address,
                'plans' => $plans,
                'vendors' => $vendors
            ]) ?>

        </div>
    </div>
</div>
