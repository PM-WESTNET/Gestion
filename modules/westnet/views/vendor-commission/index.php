<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\models\search\VendorCommissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('westnet', 'Vendor Commissions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-commission-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?=
            Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
                        'modelClass' => Yii::t('westnet', 'Commission'),
                    ]), ['create'], ['class' => 'btn btn-success'])
            ;
            ?>
        </p>
    </div>    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'vendor_commission_id',
            'name',
            'percentage',
            'value',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
