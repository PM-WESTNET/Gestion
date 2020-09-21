<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
use app\components\helpers\UserA;
use app\components\grid\ActionColumn;
use app\modules\westnet\models\NodeChangeProcess;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\NodeChangeProcess */

$this->title = 'Cambio de nodo '.$model->node_change_process_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Node Change Processes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="node-change-process-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>

        <?php if($model->getDeletable()){
            echo UserA::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->node_change_process_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]);
        } ?>
        <?php if($model->status != NodeChangeProcess::STATUS_FINISHED){
            echo UserA::a(Yii::t('app', 'Process file'), ['node-change-process/process-file', 'id' => $model->node_change_process_id], [
                'class' => 'btn btn-warning pull-right',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to process the file? This will change all the customers in the file to the selected node.'),
                    'method' => 'post',
                ],
            ]);
        } else {
            echo UserA::a(Yii::t('app', 'Generate result csv'), ['node-change-process/generate-result-csv', 'id' => $model->node_change_process_id], [
                'class' => 'btn btn-default pull-right',
            ]);
        }?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'node_change_process_id',
            'created_at',
            'ended_at',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return Yii::t('app', $model->status);
                }
            ],
            [
                'attribute' => 'node_id',
                'value' => function($model) {
                    return $model->node->name;
                }
            ],
            [
                'attribute' => 'creator_user_id',
                'value' => function($model) {
                    return $model->creatorUser->username;
                }
            ],
        ],
    ]) ?>
    <br>
    <hr>
    <h3>Historial de cambios de nodo</h3>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'old_node_id',
                'value' => function($model){
                    return $model->oldNode->name;
                }
            ],
            [
                'label' => 'Contrato',
                'value' => function($model) {
                    return $model->connection->contract_id;
                }
            ],
            [
                'attribute' => 'connection_id',
                'value' => function($model) {
                    return $model->connection_id;
                }
            ],
            [
                'attribute' => 'old_ip',
                'value' => function($model) {
                    return long2ip($model->old_ip);
                }
            ],
            [
                'attribute' => 'new_ip',
                'value' => function($model) {
                    return long2ip($model->new_ip);
                }
            ],
            
            [
                'class' => ActionColumn::class,
                'template' => '{view} {delete} {rollback}',
                'buttons' => [
                    'rollback' => function($url, $model) {
                        if ($model->status !== NodeChangeProcess::STATUS_CREATED) {
                            return Html::a('<span class="glyphicon glyphicon-repeat"></span>', ['node-change-process/rollback-history', 'history_id' => $model->node_change_history_id],['class' => 'btn btn-warning', 'title' => 'Rollback']);
                        }
                    }
                ]
            ],
        ]
    ])?>

</div>
