<?php

use app\modules\checkout\models\Payment;
use app\modules\checkout\models\search\PaymentSearch;
use app\modules\sale\models\Bill;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $searchModel PaymentSearch */
/* @var $creditProvider ActiveDataProvider */
/* @var $receiptProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Current account');
$this->params['breadcrumbs'][] = $this->title;
$contracts = new ActiveDataProvider([
    'query' => $customer->getContracts()
])
?>
<div class="payment-index">

    <h1 style="margin-bottom: auto"><?= Html::a($customer->code . ' - ' . $customer->name.' '.$customer->lastname, ['/sale/customer/view', 'id' => $customer->customer_id]); ?> <small style="padding-left: 10px; text-transform: uppercase;"><?= Html::encode($this->title) ?></small></h1>
    <h4"><?= Yii::t('app', 'Payment Code') .': '. $customer->payment_code?></h5>

    <?=$this->render('_account-detail', ['searchModel' => $searchModel, 'searchModelAccount' => $searchModelAccount]);?>

    <!--Contratos-->

    <br>
    <?php if($contracts->getCount() >= 1) {
        echo $this->render('../../../sale/views/customer/_customer-contracts', [
            'model' => $customer,
            'contracts' => $contracts,
            'products' => $products,
            'vendors' => $vendors
        ]);
    } else { ?>
        <label> <?= Yii::t('app', 'This customer doenst have any contract yet')?></label>
    <?php } ?>

    <!--Fin Contratos-->

    <div class="title">
        <p>
            <?php
            echo Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                    'modelClass' => Yii::t('app','Payment'),
                ]), ['payment/create','customer'=>$searchModel->customer_id], ['class' => 'btn btn-success']);

            if($searchModel->accountTotal()<0) {
                echo Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                        'modelClass' => Yii::t('app', 'Payment Plan')]),
                    ['payment-plan/create', 'customer_id'=>$searchModel->customer_id],
                    ['class'=>'btn btn-success pull-right']);
            }

            ?>
        </p>
    </div>

    <h2> <?= Yii::t('app','Detail') ?> </h2>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'=>Yii::t('app','Company'),
                'value'=>function($model){
                    return $model['company_name'];
                }
            ],
            [
                'label'=>Yii::t('app','Type'),
                'value'=>function($model){
                    $number = $model['status'] == Bill::STATUS_CLOSED ? ' - '.$model['number'] : '';
                    return Yii::t('app', $model['type']) . $number;
                }
            ],            [
                'label'=>Yii::t('app','Status'),
                'value'=>function($model){
                    return Yii::t('app', ucfirst($model['status']));
                }
            ],
            [
                'label'=>Yii::t('app','Date'),
                'value' => function ($model) {
                    $date_time = 'n/a';
                    // if both date and time are set
                    if (isset($model['date'], $model['time'])) {
                        $date_time = ($model['date'] . ' - ' . $model['time']);
                    }
                    // if by some chance only date is setted. (which SHOULD always.)
                    elseif (isset($model['date'])) {
                        $date_time = $model['date'];
                    }
                    return $date_time;
                },
                'format' => 'text'
            ],
            [
                'label'=>Yii::t('app','Apply to'),
                'value'=>function($model){
                    if($model['payment_id']> 0) {
                        return trim($model['bill_numbers']);
                    } else {
                        return '';
                    }
                }
            ],
            [
                'label'=>Yii::t('app','Payment Method'),
                'value'=>function($model){
                    return $model['payment_method'];
                },
            ],
            [
                'label' => Yii::t('app', 'Debit'),
                'value'=>function($model) {
                    return Yii::$app->formatter->asCurrency( ($model['bill_id']> 0) ?  $model['total'] : 0 );
                },
                'contentOptions'=>['class'=>'text-right'],
                'format' => 'raw'
            ],
            [
                'label' => Yii::t('app', 'Credit'),
                'value'=>function($model){
                    return Yii::$app->formatter->asCurrency( ($model['payment_id']> 0) ?  $model['total'] : 0 );
                },
                'contentOptions'=>['class'=>'text-right'],
                'format' => 'raw'
            ],
            [
                'label' => Yii::t('app', 'Balance'),
                'value'=>function($model){
                    return Yii::$app->formatter->asCurrency( $model['saldo'] );
                },
                'contentOptions'=>['class'=>'text-right'],
                'format' => 'raw'
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>'{update} {view} {pdf} {open} {email} {delete}',
                'urlCreator' => function($action, $model, $key, $index) {
                    if($model['bill_id']>0) {
                        $params['id'] = $model['bill_id'];
                        $params[0] = '/sale/bill/' . $action;
                    } else {
                        $params['id'] = $model['payment_id'];
                        $params[0] = 'payment/' . $action;
                    }
                    $params['return'] =  '/checkout/payment/current-account&customer='.$model['customer_id'];
                    return Url::toRoute($params);
                },
                'buttons'=>[
                    'update' => function ($url, $model, $key) {
                        return $model['status'] === 'draft' ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['class' => 'btn btn-primary']) : '';
                    },
                    'pdf' => function ($url, $model, $key) {
                        return ($model['status'] === 'completed'||$model['status'] === 'closed' ) ?
                            Html::a('<span class="glyphicon glyphicon-print"></span>', $url, ['target'=>"_blank", 'class' => 'btn btn-print']) : '';
                    },
                    'delete' => function ($url, $model, $key) {
                        if($model['status'] === 'draft' ){
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'class' => 'btn btn-danger',
                                'title' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'data-pjax' => '0',
                            ]);
                        }
                    },
                    'email' => function ($url, $model, $key) {

                        if($model['type'] !== 'Payment' && $model['status'] === 'closed' ){
                            $email = ($model['customer_id'] ? trim($model['email']) : "" );
                            $email2 = ($model['customer_id'] ? trim($model['email2']) : "" );

                            if ($email != "" && $email2 != "") {
                                return '<div class="btn-group">
                                            <a href="'. Url::toRoute(['/sale/bill/email', 'id' => $model['bill_id'], 'from' => 'account_current']).'"class="btn btn-info"><span class="glyphicon glyphicon-envelope"></span></a>
                                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>'. Html::a('Email 1: '. $email, Url::toRoute(['/sale/bill/email', 'id' => $model['bill_id'], 'from' => 'account_current', 'email' => $email]), ['title' => Yii::t('app', 'Send By Email')]).'</li>
                                                <li>'. Html::a('Email 2: '. $email2, Url::toRoute(['/sale/bill/email', 'id' => $model['bill_id'], 'from' => 'account_current', 'email' => $email2]), ['title' => Yii::t('app', 'Send By Email Secondary')]).'</li>
                                            </ul>
                                        </div>';

                            } else {
                                if ($email != "") {
                                    return  Html::a('<span class="glyphicon glyphicon-envelope"></span>', Url::toRoute(['/sale/bill/email', 'id' => $model['bill_id'], 'from' => 'account_current', 'email' => $email]), ['title' => Yii::t('app', 'Send By Email'), 'class' => 'btn btn-info']);
                                }

                                if ($email2 != "") {
                                    return  Html::a('<span class="glyphicon glyphicon-envelope"></span>', Url::toRoute(['/sale/bill/email', 'id' => $model['bill_id'], 'from' => 'account_current', 'email' => $email2]), ['title' => Yii::t('app', 'Send By Email'), 'class' => 'btn btn-info']);                                
                                }

                                return '';
                            }
                        }

                        if($model['type'] == 'Payment' && $model['status'] === Payment::PAYMENT_CLOSED ){
                            $email = ($model['customer_id'] ? trim($model['email']) : "" );
                            $email2 = ($model['customer_id'] ? trim($model['email2']) : "" );

                            if ($email != "" && $email2 != "") {
                                return '<div class="btn-group">
                                            <a class="btn btn-info" href="'. Url::toRoute(['email', 'id' => $model['payment_id'], 'from' => 'current_account']). '"><span class="glyphicon glyphicon-envelope"></span></a>
                                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>'. Html::a('Email 1: '. $email, Url::toRoute(['email', 'id' => $model['payment_id'], 'from' => 'current_account', 'email' => $email]), ['title' => Yii::t('app', 'Send By Email')]).'</li>
                                                <li>'. Html::a('Email 2: '. $email2, Url::toRoute(['email', 'id' => $model['payment_id'], 'from' => 'current_account', 'email' => $email2]), ['title' => Yii::t('app', 'Send By Email Secondary')]).'</li>
                                            </ul>
                                        </div>';

                            } else {
                                if ($email != "") {
                                    return  Html::a('<span class="glyphicon glyphicon-envelope"></span>', Url::toRoute(['email', 'id' => $model['payment_id'], 'from' => 'account_current', 'email' => $email]), ['title' => Yii::t('app', 'Send By Email'), 'class' => 'btn btn-info']);
                                }

                                if ($email2 != "") {
                                    return  Html::a('<span class="glyphicon glyphicon-envelope"></span>', Url::toRoute(['email', 'id' => $model['payment_id'], 'from' => 'account_current', 'email' => $email2]), ['title' => Yii::t('app', 'Send By Email'), 'class' => 'btn btn-info']);                                
                                }

                                return '';
                            }
                        }
                    },
                    
                ]
            ]
        ],
    ]); ?>
</div>
