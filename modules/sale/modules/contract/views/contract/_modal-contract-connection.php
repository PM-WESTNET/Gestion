<?php

use yii\helpers\Html;
use app\modules\config\models\Config;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;


?>
<div class="modal fade" id="connection-modal" role="dialog" aria-labelledby="myModalLabel" style="top:25%">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= Yii::t('westnet', 'Forced Activation') ?></h4>
            </div>
            <div class="modal-body">
                <div id="message-con"></div>
                <div class="form-group">
                    <label for="due_date" class="control-label"><?= Yii::t('westnet', 'Forced Activation Due Date') ?></label>
                    <?= DatePicker::widget([
                        'name' => 'due_date',
                        'type' => DatePicker::TYPE_INPUT,
                        'value' => (new \DateTime('now'))->format('d-m-Y'),
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy',
                        ],
                        'options' => [
                            'class' => 'form-control filter dates',
                            'placeholder' => Yii::t('app', 'Date'),
                            'id' => 'due_date'
                        ]
                    ]);
                    ?>
                </div>
                <div class="form-group">
                    <label for="due_date" class="control-label"><?= Yii::t('app', 'Reason') ?></label>
                    <textarea cols="35" rows="5" id="reason" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <?= Html::checkbox('create_product_to_invoice', false, ['id' => 'create_product'])?> <label
                            for="create_product"><?= Yii::t('app','Create Product to Invoice')?></label>

                </div>
                <div class="form-group">
                    <label for="extend_payment_product_id"><?= Yii::t('app','Product to Invoice for Extend Payment')?></label>
                    <?= Select2::widget([
                        'name' => 'extend_payment_product_id',
                        'value' => Config::getValue('extend_payment_product_id'),
                        'data' => $products,
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],
                        'options' => ['placeholder' => Yii::t('app','Select a Product'), 'id' => 'extend_product_id']
                    ])?>
                </div>
                <div class="form-group">
                    <label for="vendor_id"><?= Yii::t('app','Vendor')?></label>
                    <?= Select2::widget([
                        'name' => 'vendor_id',
                        'value' => $model->vendor_id,
                        'data' => $vendors,
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'options' => ['placeholder' => Yii::t('app','Select a Vendor'), 'id' => 'vendor_id']
                    ])?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="force-conn-bton-cancel"><?= Yii::t('app', 'Cancel') ?></button>
                <button type="button" class="btn btn-primary" onclick="ContractView.force()" data-loading-text="<?= Yii::t('app', 'Processing') ?>"><?php echo Yii::t('app', 'Update') ?></button>
            </div>
        </div>
    </div>
</div>