<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\BankCompanyConfig */

$this->title = $model->company->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bank Company Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-company-config-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->bank_company_config_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->bank_company_config_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'bank_company_config_id',
            'company_identification',
            'other_company_identification',
            'service_code',
            'other_service_code',
            'branch',
            'control_digit',
            'account_number',
            [
                'label' => Yii::t('app','Company'),
                'value' => function ($model){
                    return $model->company->name;
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
