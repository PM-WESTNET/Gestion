<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\components\helpers\UserA;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\models\search\VendorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('westnet', 'Vendors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-vendor-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <p>
            <?= UserA::a("<span class='glyphicon glyphicon-piggy-bank'></span> " . Yii::t('westnet', 'Commission Types'),
                ['vendor-commission/index'],
                ['class' => 'btn btn-default']);
            ?>
            <?= UserA::a("<span class='glyphicon glyphicon-usd'></span> " . Yii::t('westnet', 'Commissions'),
                ['vendor-liquidation/index'],
                ['class' => 'btn btn-primary']);
            ?>
            <?= UserA::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
                    'modelClass' => Yii::t('westnet', 'Vendor'),
                ]),
                ['create'],
                ['class' => 'btn btn-success'])
            ;?>
        </p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'lastname',
            [
                'attribute'=> 'username',
                'value'=>function($model){
                    return  $model->user ? $model->user->username : '';
                },
            ],
            [
                'label' => Yii::t('westnet', 'Liquidation Preview'),
                'format' => 'html',
                'value' => function($model){
                    return UserA::a(Yii::t('westnet', 'Liquidation Preview'), ['vendor-liquidation/preview', 'vendor_id' => $model->vendor_id], ['class' => 'btn btn-default']); }
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
