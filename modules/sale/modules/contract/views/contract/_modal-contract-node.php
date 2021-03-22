<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\depdrop\DepDrop;
use kartik\widgets\Select2;
use yii\widgets\ActiveForm;
use app\modules\westnet\models\Node;

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
                            
                        if ($connection->access_point_id) {
                            $data = [$connection->access_point_id => $connection->accessPoint->name];
                        }else {
                            $data = [];
                        }
                        
                        echo $form->field($connection, 'node_id')->widget(Select2::className(), [
                                'data' => yii\helpers\ArrayHelper::map($query->all(), 'node_id', 'name'),
                                'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false, 'id' => 'node_id'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ]
                            ]
                        );

            
                        echo $form->field($connection, 'access_point_id')->widget(DepDrop::class, [
                            'options' => ['id' => 'ap_id'],
                            'data' => $data,
                            //'type'=>DepDrop::TYPE_SELECT2,
                            'select2Options'=>[
                                'pluginOptions'=>[
                                    'allowClear'=>true,
                                ],
                            ],
                            'pluginOptions' => [
                                'depends' => ['node_id'],
                                'initDepends' => ['node_id'],
                                'placeholder' => Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Access Point')])."...",
                                'url' => Url::to(['/sale/contract/contract/ap-by-node'])
                            ]
                        ])->label(Yii::t('app', 'Access Point'));
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