<?php

use app\modules\westnet\models\search\ConnectionForcedHistorialSearch;
use webvimark\modules\UserManagement\models\User;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $searchModel ConnectionForcedHistorialSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('westnet', 'Connection Forced Historials');
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contract Number').': '.$contract->contract_id, 'url'=> Url::to(['/sale/contract/contract/view', 'id'=> $contract->contract_id])];
?>
<div class="connection-forced-historial-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <h3><?=  Yii::t('app', 'Customer').': ' . (isset($contract->customer)?$contract->customer->fullName:'')?></h3>
    <h3><?=  Yii::t('app', 'Address').': ' . (isset($contract->address)?$contract->address->fullAddress:'')?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'=> 'Fecha',
                'value'=> function($model){
                    return date("d-m-Y H:i:s", $model->create_timestamp);
                }   
            ],
            // 'date',
            'reason',
            [
                'label'=> Yii::t('app', 'User'),
                'value'=> function($model){
                    return User::findOne(['id'=> $model->user_id])->username;
                }   
            ]            
        ],
    ]); ?>

</div>
