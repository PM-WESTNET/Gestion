<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountMovement */

$this->title = Yii::t('accounting', 'Movement').' - '.$model->account_movement_id;
if(empty($model->dailyMoneyBoxAccount)){
    $this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Diary Book'), 'url' => ['index']];
}else{
    $this->params['breadcrumbs'][] = ['label' => $model->dailyMoneyBoxAccount->account->name, 'url' => ['money-box-account/daily-box-movements', 'id' => $model->daily_money_box_account_id]];
}
$this->params['breadcrumbs'][] = $this->title;
$diferencia = round($model->getDebt(), 2) - round($model->getCredit(),2);
?>
<div class="account-movement-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?php
            if(!empty($model->smallMoneyBoxAccount)){
                echo Html::a('<span class=\'glyphicon glyphicon-arrow-left\'></span> ' . $model->smallMoneyBoxAccount->account->name, ['money-box-account/small-box-movements', 'id' => $model->small_money_box_account_id], ['class' => 'btn btn-default']);
            }
            
            if($model->status == \app\modules\accounting\models\AccountMovement::STATE_DRAFT) {
                if ($model->isManualMovement() || ($model->getUpdatable() && \webvimark\modules\UserManagement\models\User::hasRole('modify-account-movement'))){
                    echo Html::a('<span class=\'glyphicon glyphicon-pencil\'></span>' . Yii::t('app', 'Update'), ['update', 'id' => $model->account_movement_id], ['class' => 'btn btn-primary']);
                }


                if($diferencia == 0) {
                    echo Html::a('<span class=\'glyphicon glyphicon-repeat\'></span>'.Yii::t('app', 'Close'), ['close', 'id' => $model->account_movement_id], [
                        'class' => 'btn btn-warning',
                        'data' => [
                            'confirm' => Yii::t('accounting', 'Are you sure you want to close the entry?'),
                            'method' => 'post',
                        ],]);
                }

            }
            if($model->deletable && \webvimark\modules\UserManagement\models\User::hasRole('modify-account-movement')) {
                echo Html::a('<span class=\'glyphicon glyphicon-remove\'></span>'.Yii::t('app', 'Delete'), ['delete', 'id' => $model->account_movement_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],]);
            }
            ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'account_movement_id',
            'description',
            'date:date',
            [
                'label' => Yii::t('app', 'Status'),
                'attribute' => function ($model) {
                    return Yii::t('accounting', ucfirst($model->status));
                }
            ],
        ],
    ]) ?>
    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-items" aria-expanded="true" aria-controls="panel-body-items">
            <h3 class="panel-title"><?= Yii::t('app', 'Items') ?></h3>
        </div>
        <div class="panel-body collapse in" id="panel-body-items" aria-expanded="true">
            <div class="row" id="form-list-items">
                <?php
                $items = new \yii\data\ActiveDataProvider([
                    'query' =>  $model->getAccountMovementItems()
                ]);
                echo GridView::widget([
                    'id'=>'items',
                    'dataProvider' => $items,
                    'showFooter' => true,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => Yii::t("accounting", "Account"),
                            'value' => function($model){
                                return ($model->account ? $model->account->name : '' );
                            },
                            'footer' => '<strong>'.Yii::t('app', 'Totals').'</strong>'
                        ],
                        [
                            'label' => Yii::t("accounting", "Debit"),
                            'value' => function($model){
                                return ($model->debit ? $model->debit : 0 );
                            },
                            'format' => 'currency',
                            'footer' => '<strong>'.Yii::$app->formatter->asCurrency( $model->getDebt() ) .'</strong>'
                        ],
                        [
                            'label' => Yii::t("accounting", "Credit"),
                            'value' => function($model){
                                return ($model->credit ? $model->credit : 0 );
                            },
                            'format' => 'currency',
                            'footer' => '<strong>' .Yii::$app->formatter->asCurrency( $model->getCredit() ) .'</strong>'
                        ],
                    ],
                    'options'=>[
                        'style'=>'margin-top:10px;'
                    ]
                ]);

                ?>
                <div class="panel panel-success">
                    <div class="panel-body collapse in" id="panel-body-items" aria-expanded="true">
                        <div class="col-sm-2 col-md-offset-8">
                            <?php echo Yii::t('accounting', 'Difference'); ?>
                        </div>
                        <div class="col-sm-2 <?php echo ($diferencia==0 ? 'text-success' : 'text-danger' ) ?>">
                            <strong><?php echo Yii::$app->formatter->asCurrency( $diferencia ) ?></strong>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
