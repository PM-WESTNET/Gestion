<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\partner\models\Partner */

$this->title = Yii::t('partner', 'Account Status of {name}', ['name'=>$model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('partner', 'Partners'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="title no-margin">
        <?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '. Yii::t('partner', 'Back to Partners'), ['/partner/partner'], ['class' => 'btn btn-default']) ?>
    </div>

    <div class="row">
        <?php
        $totalDebit = 0;
        $totalCredit = 0;
        $totalBalance = 0;
        foreach($status as $key=>$value) {
            $totalDebit += $value['debit'];
            $totalCredit += $value['credit'];
            $totalBalance += $value['balance'];
            ?>
            <div class="panel panel-default" id="panel_operation_type">
                <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-items" aria-expanded="true" aria-controls="panel-body-items">
                    <h3 class="panel-title"><?php echo $value['name'] ?></h3>
                </div>
                <div class="panel-body collapse in" id="panel-body-items" aria-expanded="true">
                    <div class="row">
                        <div class="col-sm-4 text-center"><?php echo Yii::t('app', 'Debit')?></div>
                        <div class="col-sm-4 text-center"><?php echo Yii::t('app', 'Credit')?></div>
                        <div class="col-sm-4 text-center"><?php echo Yii::t('partner', 'Withdraw pending')?></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 text-center"><?php echo Yii::$app->formatter->asCurrency($value['debit']) ?></div>
                        <div class="col-sm-4 text-center"><?php echo Yii::$app->formatter->asCurrency($value['credit']) ?></div>
                        <div class="col-sm-4 text-center <?php echo ( $value['balance'] < 0 ? 'label label-danger' : '') ?>"><?php echo Yii::$app->formatter->asCurrency($value['balance']) ?></div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="row">
        <div class="panel panel-default" id="panel_operation_type">
            <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-items" aria-expanded="true" aria-controls="panel-body-items">
                <h3 class="panel-title"><?php echo Yii::t('app', 'Total') ?></h3>
            </div>
            <div class="panel-body collapse in" id="panel-body-items" aria-expanded="true">
                <div class="row">
                    <div class="col-sm-4 text-center"><?php echo Yii::t('app', 'Debit')?></div>
                    <div class="col-sm-4 text-center"><?php echo Yii::t('app', 'Credit')?></div>
                    <div class="col-sm-4 text-center"><?php echo Yii::t('partner', 'Withdraw pending')?></div>
                    <div class="col-xs-12">
                        <hr>                        
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-4 text-center"><?php echo Yii::$app->formatter->asCurrency($totalDebit) ?></div>
                    <div class="col-sm-4 text-center"><?php echo Yii::$app->formatter->asCurrency($totalCredit) ?></div>
                    <div class="col-sm-4 text-center"><?php echo Yii::$app->formatter->asCurrency(abs($totalBalance)) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
