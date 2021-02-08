<?php

use kartik\export\ExportMenu;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('accounting', 'Movements') . " - " . $model->moneyBoxAccount->moneyBox->name . " - " . $model->moneyBoxAccount->number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Money Box Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-movement-index">

    <h1><?= Html::encode($this->title) ?> <small><?= $searchModel->date ?></small></h1>


    <p>
        <?php
        if ($model->isOpen()) {

            //Nueva entrada
            echo Html::a(
                    '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('accounting', 'Create Entry'), 
                    ['/accounting/account-movement/small-box-create', 'box_id' => $model->money_box_account_id], 
                    ['class' => 'btn btn-success']
            );
            echo ' ';

            //Cierre de caja
            echo Html::a(
                    '<span class="glyphicon glyphicon-repeat"></span> ' . Yii::t('app', 'Close'), ['/accounting/money-box-account/close-small-box', 'id' => $model->small_box_id], ['class' => 'btn btn-warning',
                'data' => [
                    'confirm' => Yii::t('accounting', 'Are you sure you want to close this box?'),
                    'method' => 'post',
                ]]
            );
        } else {
            echo \yii\bootstrap\Alert::widget([
                'options' => [
                    'class' => 'alert-info',
                ],
                'body' => Yii::t('accounting', 'This small box is closed.'),
                    ]
            );
        }
        ?>
    </p>
        <?php
        /**
        $item = '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters');

        echo \yii\bootstrap\Collapse::widget([
            'items' => [
                [
                    'label' => $item,
                    'content' => $this->render('_daily-box-search', ['model' => $searchModel]),
                    'encode' => false,
                ],
            ]
        ]);
        **/
        // Renders a export dropdown menu
        echo ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'date',
                'description',
                'debit:currency',
                'credit:currency',
            ],
            'showConfirmAlert' => false
        ]);


        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'header' => Yii::t('app', 'Date'),
                    'attribute' => 'date',
                    'format' => ['date']
                ],
                [
                    'header' => Yii::t('app', 'Description'),
                    'attribute' => 'description',
                ],
                [
                    'header' => Yii::t('accounting', 'Debit'),
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model['debit']);
                    }
                ],
                [
                    'header' => Yii::t('accounting', 'Credit'),
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model['credit']);
                    }
                ],
                [
                    'header' => Yii::t('app', 'Status'),
                    'value' => function ($model) {
                        return Yii::t('app', ucfirst($model['status']));
                    }
                ]
            ],
        ]);
        ?>
    

        <?php //Balance total ?>
    <div class="row">
        <div class="col-sm-6 col-sm-offset-6"><h3><?= Yii::t('accounting', 'Total Account') ?></small></h3></div>
    </div>
    <div class="row">
        <div class="col-sm-6">&nbsp;</div>
        <div class="col-sm-2 text-center">
            <strong><?= Yii::t('app', 'Balance'); ?></strong>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">&nbsp;</div>
        
        <div class="col-sm-2 text-center <?= ($model->balance < 0 ? 'alert-danger' : '' ) ?>">
            <?= Yii::$app->formatter->asCurrency($model->balance); ?>
        </div>
    </div>

</div>