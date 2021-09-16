<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\NatServer */

$this->title = Yii::t('app', 'Nat Server'). ': '. $model->description;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Nat Server'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nat-server-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if($model->deletable) echo Html::a('Delete', ['delete', 'id' => $model->nat_server_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app','Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'description',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <br>

    <h3>Nodos</h3>
    <hr>
    <?= GridView::widget([
        'dataProvider' => $dataProviderNodes,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'status',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
