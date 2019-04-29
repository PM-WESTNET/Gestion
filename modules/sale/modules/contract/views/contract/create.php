<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\Contract */

$this->title = Yii::t('app', 'Create Contract').': '. $customer->fullName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['/sale/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $customer->fullName, 'url' => ['/sale/customer/view', 'id' => $model->customer_id]];
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contracts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="contract-create">

            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render('_form', [
                'same_address' => $same_address,
                'model' => $model,
                'customer'=>$customer,
                'address'=>$address,
                'contractDetailPlan'=>$contractDetailPlan,
                'contractDetailIns' => $contractDetailIns,
                'instalationProd' => $instalationProd,
                'plans' => $plans,
                'vendors' => $vendors
            ]) ?>

        </div>
    </div>
</div>

