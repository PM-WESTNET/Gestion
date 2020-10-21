<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataImport */

$this->title = $model->firstdata_import_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Firstdata Imports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firstdata-import-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->firstdata_import_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->firstdata_import_id], [
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
            'firstdata_import_id',
            'presentation_date',
            'created_at',
            'status',
            'response_file',
            'observation_file',
        ],
    ]) ?>

</div>
