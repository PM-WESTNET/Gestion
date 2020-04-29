<?php

use app\modules\westnet\models\Node;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="modal fade" id="node-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="top:25%">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo Yii::t('westnet', 'Change Node') ?></h4>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin(['id' => 'form-node']); ?>
                <div class="form-group field-contract-node-id required">

                    <?php
                    Html::label(Yii::t('westnet', 'Node'));
                    if ($connection) {
                        $query = Node::find();
                        $query->select(['node.node_id', 'concat(node.name, \' - \', s.name) as name'])
                            ->leftJoin('server s', 'node.server_id = s.server_id');

                        echo $form->field($connection, 'node_id')->widget(Select2::className(), [
                                'data' => yii\helpers\ArrayHelper::map($query->all(), 'node_id', 'name'),
                                'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ]
                            ]
                        );
                    }
                    ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel') ?></button>
                <?php
                echo Html::a(Yii::t('app', 'Update'), null, [
                    'class' => 'btn btn-primary',
                    'id' => 'btn-change-node',
                    'data-loading-text' => Yii::t('app', 'Processing') . "..."
                ]);
                ?>
            </div>
        </div>
    </div>
</div>