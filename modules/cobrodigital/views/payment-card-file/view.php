<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\cobrodigital\models\PaymentCardFile */

$this->title = $model->payment_card_file_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Card Files'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-card-file-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->payment_card_file_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->getDeletable()) {
            echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->payment_card_file_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]);
        }?>

        <?= Html::a(Yii::t('cobrodigital', 'Confirm import'), ['import', 'id' => $model->payment_card_file_id], [
                'class' => 'btn btn-default pull-right',
                'data' => [
                    'confirm' => Yii::t('app',  'Are you sure you want to import this file?')
                ]
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'payment_card_file_id',
            'upload_date',
            'file_name',
            'path:ntext',
        ],
    ]) ?>

</div>
