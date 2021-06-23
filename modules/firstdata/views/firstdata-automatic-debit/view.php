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
            [
                'label' => Yii::t('app', 'Credit Card'),
                'format' => 'raw',
                'value' => function($model) {
                    if(!$model->hiddenCreditCard){
                        Yii::$app->session->setFlash('error', "No se encuentra disponible en este momento el servicio que devuelve esta información. Intente nuevamente más tarde o comuníquese con el administrador del sistema.");
                        return "<p class='not-set'>(Servicio no disponible)</p>";
                    }else{
                        return $model->hiddenCreditCard;
                    }
                }
            ],
            'status',
            [
                'label' => Yii::t('app', 'User'),
                'value' => function($model) {
                    if (!empty($model->user)){
                        return $model->user->username;
                    }
                }
            ],
            [
                'attribute' => 'adhered_by',
                'value' => function($model) {
                     return $model->adhered_by;
                }
            ],
            'created_at:date'
            
        ],
    ]) ?>

</div>
