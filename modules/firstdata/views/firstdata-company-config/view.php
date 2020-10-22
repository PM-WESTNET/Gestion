<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataCompanyConfig */

$this->title = Yii::t('app', 'Firstdata Config') . ': '. $model->company->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Firstdata Company Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firstdata-company-config-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' .Yii::t('app', 'Update'), ['update', 'id' => $model->firstdata_company_config_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ' .Yii::t('app', 'Delete'), ['delete', 'id' => $model->firstdata_company_config_id], [
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
            'firstdata_company_config_id',
            'commerce_number',
            'company_id',
        ],
    ]) ?>

</div>
