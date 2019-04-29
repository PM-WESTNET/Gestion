<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sale\models\search\CustomerLog */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Customer Logs') . $customer->fullName;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-log-index">

    <h1><?= Yii::t('app', 'Customer Logs') . Html::a($customer->code . ' - ' . $customer->fullName, ['/sale/customer/view', 'id' => $customer->customer_id]); ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

     <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],            
            'action',
            'before_value',
            'new_value',
            'date',
            'observations',
            [
              'label' => Yii::t('app', 'User'),
              'value' => function ($model){
                    $user= \webvimark\modules\UserManagement\models\User::findIdentity($model->user_id);
                    return ($user !== null ? $user->username : '');
              }
            ],
            
        ],
    ]); ?>

</div>
