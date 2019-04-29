<?php

use app\modules\westnet\ecopagos\EcopagosModule;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = EcopagosModule::t('app', 'Credentials');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="credential-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?=
        Html::a("<span class='glyphicon glyphicon-plus'></span> " . EcopagosModule::t('app', 'Create Credential'), ['create'], ['class' => 'btn btn-success'])
        ;
        ?>
    </p>


    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'credential_id',
            [
                'attribute' => 'customer_id',
                'value' => function($model) {
                    if (!empty($model->customer)) {
                        return $model->customer->name;
                    }
                }
            ],
            [
                'attribute' => 'cashier_id',
                'value' => function($model) {
                    if (!empty($model->cashier)) {
                        return $model->cashier->getCompleteName();
                    }
                }
            ],
            'datetime:datetime',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    if (!empty($model->status)) {
                        return $model->fetchStatuses()[$model->status];
                    }
                }
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{view}{delete}',
            ],
        ],
    ]);
    ?>

</div>
