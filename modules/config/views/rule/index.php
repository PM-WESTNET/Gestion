<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\config\ConfigModule;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\config\models\search\RuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ConfigModule::t('config', 'Rules');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rule-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . ConfigModule::t('config', 'Create {modelClass}', [
        'modelClass' => 'Rule',
    ]), 
            ['create'], 
            ['class' => 'btn btn-success']) 
            ;?>
        </p>
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'rule_id',
            [
                'attribute' => 'item.label',
                'header' => Yii::t('app', 'Item')
            ],
            'message',
            'max',
            'min',
            'pattern',
            // 'format',
            // 'targetAttribute',
            // 'targetClass',
            // 'item_id',
            // 'validator',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
