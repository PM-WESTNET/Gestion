<?php 
/**
 * Botones para generar comprobantes validos a partir de el tipo del model actual
 */

use app\modules\sale\components\BillExpert;

if(!$model) return;
if(!$model->customer || !$model->active) return;
if($model->status == 'draft') return; ?>

<?php 
foreach($model->billType->billTypes as $billType):
    if($model->customer->checkBillType($billType) && BillExpert::checkAccess('create', $billType->class)):
    ?>
        <a class="btn btn-primary" onclick="Bill.generate(<?= $billType->bill_type_id ?>)" title="<?= Yii::t('app', 'Create a new {model_name} from this {parent_name}', ['model_name'=>$billType->name, 'parent_name'=>$model->billType->name]) ?>"><span class="glyphicon glyphicon-plus"></span> <?= $billType->name ?></a>
    <?php 
    endif;
endforeach; ?>
