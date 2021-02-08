<?php

use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\modules\afip\models\TaxesBook */

?>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <strong>
                <?=Yii::t('afip', 'Bill Totals')?>
            </strong>
        </div>
        <div class="panel-body">
            <?php foreach($totals as $key=>$total) { ?>
            <div class="row ">
                <div class="col-sm-8">
                    <?=Yii::t('afip', $key)?>
                </div>
                <div class="col-sm-4 text-right">
                    <?=Yii::$app->getFormatter()->asCurrency($total)?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
