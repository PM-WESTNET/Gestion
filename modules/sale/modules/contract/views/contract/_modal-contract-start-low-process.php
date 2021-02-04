<?php

use app\modules\config\models\Config;
use app\modules\ticket\models\Category;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

?>

<div class="modal fade" id="start-low-process-modal" role="dialog" aria-labelledby="start-low-process-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="start-low-process-label"><?php echo Yii::t('app', 'Low Process') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label><?= Yii::t('westnet', 'Reason of low') ?></label>
                    </div>
                    <div class="col-md-12">
                        <?php $categories = Category::getForSelectChilds(Config::getValue('mesa_category_low_reason'));
                        echo Select2::widget([
                            'name' => 'category_id',
                            'data' => ArrayHelper::map($categories, 'category_id', 'name'),
                            'options' => ['placeholder' => Yii::t("westnet", "Select an reason..."), 'encode' => false],
                            'pluginOptions' => [
                                'allowClear' => true
                            ]
                        ]);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label><?= Yii::t('westnet', 'Date of low') ?></label>
                    </div>
                    <div class="col-md-12">
                        <?= DatePicker::widget([
                            'type' => 1,
                            'language' => Yii::$app->language,
                            'name' => 'date_low',
                            'value' => (new \DateTime('now'))->format('d-m-Y'),
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'dd-mm-yyyy',
                            ],
                            'options'=>[
                                'class'=>'form-control filter dates',
                                'placeholder'=>Yii::t('app','Date'),
                                'id' => 'date_low',
                            ]
                        ]);
                        ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-12">
                        <?= Html::checkbox('credit', false, ['id' => 'credit_note'])?>
                        <label for=""><?= Yii::t('app','Create Credit Note')?></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Yii::t('app', 'Cancel') ?></button>
                <button type="button" class="btn btn-primary" id="start-low-button"><?= Yii::t('app', 'Low') ?></button>
            </div>
        </div>
    </div>
</div>