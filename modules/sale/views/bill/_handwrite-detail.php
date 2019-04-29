<?php
use app\modules\sale\models\Unit;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\TaxRate;
use app\modules\sale\models\forms\BillDetailForm;

if(!\webvimark\modules\UserManagement\models\User::canRoute('/sale/bill/handwrite-detail')) return;

$detail = new BillDetailForm;
?>
 <div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default hidden-print">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::t('app', 'Handwrite detail') ?></h3>
            </div>
            <div class="panel-body">

                <?php $form = ActiveForm::begin([
                    'id'=>'handwrite-detail-form',
                    'action' => ['handwrite-detail', 'id' => $model->bill_id]
                ]); ?>

                <div class="row">
                    <div class="col-sm-3 col-md-2">
                        <?= $form->field($detail, 'unit_id')->dropDownList( ArrayHelper::map( Unit::find()->all(), 'unit_id', 'name' ) ) ?>
                    </div>

                    <div class="col-sm-3 col-md-1">
                        <?= $form->field($detail, 'qty')->textInput(['id'=>'handwrite-detail-qty']) ?>
                    </div>
                    <div class="col-sm-9 col-md-4">
                        <?= $form->field($detail, 'concept')->textInput(['id'=>'handwrite-detail-concept']) ?>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <?= $form->field($detail, 'unit_net_price', [
                            'template' => '{label}<div class="input-group" style="z-index:0;"><span class="input-group-addon">$</span>{input}</div> {error}'
                        ])->textInput(['id'=>'handwrite-detail-net']) ?>
                    </div>


                    <?php 
                    //IVA:
                    $tax = \app\modules\sale\models\Tax::find()->where(['slug' => 'iva'])->one();
                    if($tax):
                    ?>
                    
                        <div class="col-sm-6 col-md-2">
                            <?= $form->field($detail, 'tax_rate_id')->dropDownList(ArrayHelper::map(TaxRate::findRates('iva'), 'tax_rate_id', 'name'))->label($tax->name) ?>
                        </div>
                    
                    <?php endif; ?>
                    
                </div>
                <div class="row">
                    <div class="col-sm-12 text-right">
                        <div class="btn btn-success inline-block" id="handwrite-detail-add">
                            <span class="glyphicon glyphicon-plus"></span> <?= Yii::t('app', 'Add') ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>