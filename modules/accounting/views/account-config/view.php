<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountConfig */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Account Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-config-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->account_config_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->account_config_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'account_config_id',
            'name',
            'class',
            'classMovement',
        ],
    ]) ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('accounting', 'Assigned Accounts') ?></h3>
        </div>
        <div class="panel-body">

            <?=GridView::widget([
                'id'=>'grid',
                'dataProvider' => new ActiveDataProvider([
                    'query' => $model->getAccountConfigHasAccounts(),
                ]),
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'account.name',
                    'is_debit:boolean',
                    'attrib'
                ],
                
                'options'=>[
                    'style'=>'margin-top:10px;'
                ]
            ]);
            ?>
        </div>
    </div>
</div>
