<?php

use yii\helpers\Html;
use app\modules\checkout\models\PagoFacilTransmitionFile;
use yii\grid\GridView;
use yii\helpers\Url;

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

    <?= GridView::widget([
        'dataProvider'=> $dataProvider,
        'columns' => [
            'upload_date',
            [
                'label' => Yii::t('app', 'Account'),
                'value' => function (PagoFacilTransmitionFile $model){
                    if($model->moneyBoxAccount) {
                        return $model->moneyBoxAccount->account->name . ' - ' . $model->moneyBoxAccount->moneyBox->name;
                    }
                    return '';
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
                'template'=>'{view} {delete}',
                'buttons'=>[
                    'view' => function ($url, $model, $key) {
                        return  Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::toRoute(['payment/pagofacil-payment-view', 'idFile'=>$model->pago_facil_transmition_file_id]), ['class' => 'btn btn-view']);
                    },
                    'delete' => function ($url, $model, $key) {
                        if($model->getDeletable()) {
                            return  Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['payment/delete-pago-facil-transmition-file', 'id'=>$model->pago_facil_transmition_file_id]), ['class' => 'btn btn-danger']);
                        }

                        return '';
                    },
                ],
            ],
        ],
    ])?>
</div>    