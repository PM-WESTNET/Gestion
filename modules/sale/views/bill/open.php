<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Bill $model
 */

$this->title = Yii::t('app', 'Open {modelClass}: ', [
  'modelClass' => Yii::t('app','Bill'),
]) . $model->bill_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bills'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->bill_id, 'url' => ['view', 'id' => $model->bill_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Open');
?>
<div class="bill-open">

    <?php if(!$embed): ?>
    <h1><?= Html::encode(Yii::t('app', 'Open {modelClass}', ['modelClass' => Yii::t('app', 'Bill')]) ); ?></h1>
    <?php endif; ?>
    
    <div class="panel panel-default" style="margin-top: 40px;">
        <div class="panel-body" style="text-align: center; padding: 60px;">
            <p class="lead">
                <?= Yii::t('app', 'This bill is completed and ready to be payed. Are you sure you want to open it again?') ?>
            </p>

            <?php $form = ActiveForm::begin(['id'=>'bill-form']); ?>

            <div class="row" style="margin: 50px;">
                <div class="col-sm-2 col-sm-offset-3">
                    <a class="btn btn-danger btn-lg" href="<?= yii\helpers\Url::toRoute(['bill/view', 'id'=>$model->bill_id]) ?>"> 
                        <span class="glyphicon glyphicon-remove"></span>
                        <?= Yii::t('app', 'Cancel') ?> 
                    </a>
                </div>
                <p class="visible-xs"></p>
                <div class="col-sm-2 col-sm-offset-2">
                    <button type="submit" class="btn btn-primary btn-lg" name="continue" value="1">
                        <span class="glyphicon glyphicon-ok"></span>
                        <?= Yii::t('app', 'Continue') ?> 
                    </button>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
    
    
</div>
