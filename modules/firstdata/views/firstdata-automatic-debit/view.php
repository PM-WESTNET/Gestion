<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataAutomaticDebit */

$this->title = Yii::t('app', 'Firstdata Automatic Debit'). ': '. $model->customer->fullName . ' ('. $model->customer->code . ')';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Firstdata Automatic Debits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firstdata-automatic-debit-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->firstdata_automatic_debit_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->firstdata_automatic_debit_id], [
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
            'firstdata_automatic_debit_id',
            [
                'attribute' => 'customer_id',
                'value' => function($model) {
                    return $model->customer->fullName;
                }
            ],
            [
                'label' => Yii::t('app','Code'),
                'value' => function($model) {
                    return $model->customer->code;
                }
            ],
            [
                'attribute' => 'company_config_id',
                'value' => function($model) {
                    return $model->companyConfig->company->name;
                }
            ],
        ],
    ]) ?>

</div>
