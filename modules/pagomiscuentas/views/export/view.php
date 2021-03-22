<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \app\modules\pagomiscuentas\models\PagomiscuentasFile */

if (empty($model->from_date)) {
    $this->title = Yii::t('pagomiscuentas', 'Export of {name} to {date}', ['name'=>$model->company->name, 'date'=>$model->date ]);
}else {
    $this->title = Yii::t('pagomiscuentas', 'Export of {name} from {from} to {date}', ['name'=>$model->company->name, 'date'=>$model->date, 'from' => $model->from_date]);
}

$this->params['breadcrumbs'][] = ['label' => Yii::t('pagomiscuentas', 'PagoMisCuentas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-view">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?php if($model->deletable) echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->pagomiscuentas_file_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
            <?php if($model->status == \app\modules\pagomiscuentas\models\PagomiscuentasFile::STATUS_DRAFT) {
                echo Html::a(Yii::t('app', 'Close'), ['close', 'id' => $model->pagomiscuentas_file_id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => Yii::t('pagomiscuentas', 'Are you sure you want to close this item?'),
                    'method' => 'post',
                ],
            ]);
            }?>

        </p>
    </div>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'company.name',
            'from_date',
            'date'
        ],
    ]) ?>

</div>
