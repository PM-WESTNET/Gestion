<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\backup\models\Backup */

$this->title = $model->backup_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Backups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backup-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->backup_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->backup_id], [
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
            'backup_id',
            'init_timestamp:datetime',
            'finish_timestamp:datetime',
            'status',
            'description:ntext',
        ],
    ]) ?>

</div>
