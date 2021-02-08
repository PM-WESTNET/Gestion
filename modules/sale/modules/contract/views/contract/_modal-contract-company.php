<?php

use app\modules\sale\models\Company;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="modal fade" id="company-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="top:25%">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo Yii::t('westnet', 'Change Company') ?></h4>
            </div>
            <div class="modal-body">
                <?php
                $form = ActiveForm::begin(['id' => 'form-company']);
                if ($connection) {
                    $connection->company_id = ($connection->company_id ? $connection->company_id : ($connection->node ? $connection->node->company_id : $model->customer->company_id) );

                    echo $form->field($connection, 'company_id')
                        ->label(Yii::t('app', 'Company'))
                        ->dropDownList(ArrayHelper::map(Company::find()->all(), 'company_id', 'name'), [
                            'prompt' => Yii::t('app', 'Select')
                        ]);
                }
                ?>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel') ?></button>
                <?php
                echo Html::a(Yii::t('app', 'Update'), null, [
                    'class' => 'btn btn-primary',
                    'id' => 'btn-change-company',
                    'data-loading-text' => Yii::t('app', 'Processing') . "..."
                ]);
                ?>
            </div>
        </div>
    </div>
</div>