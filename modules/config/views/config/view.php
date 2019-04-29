<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\config\ConfigModule;

/* @var $this yii\web\View */
/* @var $model app\modules\config\models\Config */

$this->title = $model->config_id;
$this->params['breadcrumbs'][] = ['label' => ConfigModule::t('config', 'Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a(ConfigModule::t('config', 'Update'), ['update', 'id' => $model->config_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a(ConfigModule::t('config', 'Delete'), ['delete', 'id' => $model->config_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => ConfigModule::t('config', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'config_id',
            'item_id',
            'value:ntext',
        ],
    ]) ?>

</div>
