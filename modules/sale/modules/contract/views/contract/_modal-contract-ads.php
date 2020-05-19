<?php

?>
<div class="modal fade" id="ads-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="top:25%">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= Yii::t('westnet', 'Select Node') ?></h4>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <label><?= Yii::t('westnet', 'Node') ?></label>
                    <select id="node_id" class="form-control"></select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'Cancel') ?></button>
                <button type="button" class="btn btn-primary" id="print-button"><?= Yii::t('app', 'Print') ?></button>
            </div>
        </div>
    </div>
</div>