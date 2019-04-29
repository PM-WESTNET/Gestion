<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\zone\models\Zone;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\models\search\NodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('westnet','Assigned IPs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="node-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php

    $item = '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters');

    echo \yii\bootstrap\Collapse::widget([
        'items' => [
            [
                'label' => $item,
                'content' => $this->render('_search-assigned-ip', ['model' => $searchModel]),
                'encode' => false,
            ],
        ]
    ]);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'=> Yii::t('app', 'Customer'),
                'attribute'=> 'contract.customer.fullName',
            ],
            [
                'label'=> Yii::t('app', 'Customer Number'),
                'attribute'=> 'contract.customer.code',
            ],
            [
                'label'=> Yii::t('westnet', 'Server'),
                'attribute'=> 'server.name'
            ],
            [
                'label'=> Yii::t('westnet', 'Node'),
                'attribute'=> 'node.name'
            ],
            [
                'label'=> Yii::t('westnet', 'ip4_1'),
                'value' => function($model) {
                    return long2ip($model->ip4_1);
                }
            ],
            [
                'label'=> Yii::t('westnet', 'ip4_2'),
                'value' => function($model) {
                    return long2ip($model->ip4_2);
                }
            ],
            [
                'label'=> Yii::t('westnet', 'ip4_public'),
                'value' => function($model) {
                    return long2ip($model->ip4_public);
                }
            ],
        ],
    ]); ?>
</div>
