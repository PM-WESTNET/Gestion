<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/03/19
 * Time: 11:36
 */

use app\modules\sale\modules\contract\models\search\ContractSearch;
use app\modules\westnet\models\Connection;
use yii\grid\GridView;
use app\components\helpers\UserA;
use yii\helpers\Html;
use app\modules\sale\modules\contract\models\Contract;
use kartik\widgets\DatePicker;
use kartik\select2\Select2;
use app\modules\config\models\Config;
use yii\helpers\Url;
use webvimark\modules\UserManagement\models\User;
?>
<h2> <?= Yii::t('app', 'Contracts') ?> </h2>

<?php $connection_id = 0; ?>
<?= GridView::widget([
    'dataProvider' => $contracts,
    'summary' => false,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'contract_id',
        'from_date',
        [
            'label'=> Yii::t('app', 'Status Account'),
            'value'=>  function($model){
                $con = $model->connection;
                return (!empty($con) ? Yii::t('app', ucfirst($con->status_account). ' Account'): null);
            }
        ],
        [
            'label'=> Yii::t('app', 'Address'),
            'value'=> function($model){
                return $model->address ? $model->address->shortAddress : '';
            }
        ],
        [
            'label' => Yii::t('app', 'Plan'),
            'value' => function ( $model){
                return $model->getPlan()->name;
            }
        ],
        ['class' => 'yii\grid\ActionColumn',
            'template'=>'{view} {update} {force-connection}',
            'buttons'=>[
                'view' => function ($url, $model) {
                    if(User::hasPermission('contract-view')){
                        return UserA::a('<span class="glyphicon glyphicon-eye-open"></span>',['/sale/contract/contract/view',  'id' => $model->contract_id], [
                            'title' => Yii::t('yii', 'View'),
                            'class' => 'btn btn-view'
                        ]);
                    }
                },
                'update' => function ($url, $model) {
                    if ($model->canUpdate()){
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',['/sale/contract/contract/update',  'id' => $model->contract_id], [
                            'title' => Yii::t('yii', 'Update'),
                            'class' => 'btn btn-primary'
                        ]);
                    }
                },
                'force-connection' => function($url, $model) use ($products, $vendors){
                    if (Yii::$app->getModule('westnet')) {
                        $connection = $model->connection;
                        if($connection) {
                            if($model->status == Contract::STATUS_ACTIVE && User::canRoute('/westnet/connection/force')) {
                                return Html::a(Yii::t('westnet', 'Force Activation'), null, [
                                    'class' => 'btn btn-danger',
                                    'data-loading-text' => Yii::t('westnet', 'Enabling') . "...",
                                    'data-toggle' => 'modal',
                                    'data-target' => '#connection-'.$connection->connection_id,
                                    'data-confirm' => Yii::t('westnet', 'Are you sure you want to force the activation of this connection?')
                                ]);
                            }
                        }
                    }
                }
            ],
        ],
    ],
]);

//Modales para el forzado de las connexiones
foreach ($contracts->getModels() as $contract) {
    $connection = $contract->connection;
    if($connection) { ?>

    <div class="modal fade" id="<?='connection-'.$connection->connection_id?>" role="dialog" aria-labelledby="myModalLabel" style="top:25%">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?= Yii::t('westnet', 'Forced Activation') ?></h4>
                </div>
                <div class="modal-body">

                    <div class="errormessages"></div>
                    <div class="form-group">
                        <label for="due_date"
                               class="control-label"><?= Yii::t('westnet', 'Forced Activation Due Date') ?></label>
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
                        <?= Html::checkbox('create_product_to_invoice', false, ['id' => 'create_product']) ?>
                        <label
                                for="create_product"><?= Yii::t('app', 'Create Product to Invoice') ?></label>

                    </div>
                    <div class="form-group">
                        <label for="extend_payment_product_id"><?= Yii::t('app', 'Product to Invoice for Extend Payment') ?></label>
                        <?= Select2::widget([
                            'name' => 'extend_payment_product_id',
                            'value' => Config::getValue('extend_payment_product_id'),
                            'data' => $products,
                            'pluginOptions' => [
                                'allowClear' => false,
                            ],
                            'options' => ['placeholder' => Yii::t('app', 'Select a Product'), 'id' => 'extend_product_id']
                        ]) ?>
                    </div>
                    <div class="form-group">
                        <label for="vendor_id"><?= Yii::t('app', 'Vendor') ?></label>
                        <?= Select2::widget([
                            'name' => 'vendor_id',
                            'value' => $contract->vendor_id,
                            'data' => $vendors,
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                            'options' => ['placeholder' => Yii::t('app', 'Select a Vendor'), 'id' => 'vendor_id']
                        ]) ?>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'Cancel') ?></button>
                    <button type="button" class="btn btn-primary" id="force-connection" onclick="ContractList.force('<?= Url::to(['/westnet/connection/force', 'id' => $connection->connection_id])?>')"
                            data-loading-text="<?= Yii::t('app', 'Processing') ?>"><?= Yii::t('app', 'Update') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php } } ?>

<script>
    var ContractList = new function () {

        this.force = function (url) {
            if ($('#due_date').val() !=='' && $('#reason').val() !== '' ) {
                $('#connection-modal').modal('hide')
                if ($('#due_date').kvDatepicker('getDate') != null) {

                    $.ajax({
                        url: url,
                        data: {
                            due_date: $('#due_date').val(),
                            reason: $('#reason').val(),
                            create_product: $('#create_product').is(':checked'),
                            product_id: $('#extend_product_id').val(),
                            vendor_id: $('#vendor_id').val()
                        },
                        method: 'POST',
                        success: function (data) {
                            if (data.status == 'success') {
                                window.location.reload();
                                return true;
                            } else {
                                $('#force-connection').button('reset');
                                if (data.message) {
                                    $('.errormessages').html('<div class="alert alert-danger">'+data.message +'</div>');
                                } else {
                                    alert('Error');
                                }
                                return false;
                            }
                        }
                    });
                }
            }else{
                $('.errormessages').html('<div class="alert alert-danger">Por favor, complete fecha de vencimiento y motivo.</div>');
            }
        }
    }
</script>

<?php $this->registerJs('ContractList') ?>
