<?php

use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title= Yii::t('app', 'Pago Fácil Files');
$this->params['breadcrumbs'][]= $this->title;
?>
<div class="pagofacil-index">
     <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Importar Archivo', [
                
            ]), ['pagofacil-payments-import'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    
    
    <?= \yii\grid\GridView::widget([
        'dataProvider'=> $dataProvider,
        'columns' => [
            'upload_date',
            [
                'label' => Yii::t('app', 'Account'),
                'value' => function (app\modules\checkout\models\PagoFacilTransmitionFile $model){
                    $account= app\modules\accounting\models\MoneyBoxAccount::findOne(['money_box_account_id' => $model->money_box_account_id]);
                    return $account->account->name . ' - ' . $account->moneyBox->name;
                } 
            ],
            'total:currency',
            [
                'header'=>Yii::t('app', 'Status'),
                'value' => function ($model) {
                    return Yii::t('app', ucfirst($model['status']));
                }
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>'{view} ',
                'buttons'=>[
                    'view' => function ($url, $model, $key) {
                        return  Html::a('<span class="glyphicon glyphicon-eye-open"></span>', yii\helpers\Url::toRoute(['payment/pagofacil-payment-view', 'idFile'=>$model->pago_facil_transmition_file_id]), ['class' => 'btn btn-view']);
                    },
                ],
            ],
        ],
    ])?>
</div>    