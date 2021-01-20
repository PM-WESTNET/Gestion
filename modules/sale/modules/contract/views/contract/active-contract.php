<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\depdrop\DepDrop;
use kartik\widgets\Select2;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\widgets\DatePicker;
use app\modules\westnet\models\Node;
use app\modules\config\models\Config;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\Contract */

$this->title = ($model->status=='draft' ? Yii::t('app', 'Active Contract') : Yii::t('app', 'Update') . " " . Yii::t('app', 'Contract') )  ." - " .
    $model->customer->fullName . " - " . Yii::t('app', 'Contract Number') .": " . $model->contract_id;
$this->params['breadcrumbs'][] = ['label' => $model->customer->fullName, 'url' => ['/sale/customer/view', 'id'=> $model->customer_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Contract Number') .": " . $model->contract_id;
$this->params['breadcrumbs'][] = Yii::t('app', 'Contract');
try{
    $invoice_free_days = Config::getValue('contract_days_for_invoice_next_month');
} catch(\Exception $ex){
    $invoice_free_days = 0;
}
?>
<div class="contract-update">

    <h2><?= Html::encode($this->title) ?></h2>

    <?php $form = ActiveForm::begin([
        'id' => 'form_invoice'
    ]); ?>
    <?php  echo Html::hiddenInput('Contract[contract_id]', $model->contract_id);

        // si tiene una item facturado en el periodo qe eligio, muestro fecha superior a este mes
        $startDate = new \DateTime('now');
        foreach( $model->getContractDetails()->all() as $contractDetail ) {
            if ( $contractDetail->isInvoiced($startDate) ) {
                $startDate->modify('+1 month');
                break;
            }
        }
        echo Html::hiddenInput('Contract['.($action=='active' ? 'from_date' : 'to_date' ).']', $startDate->format('d-m-Y'));
    ?>
    <div class="contract-form">
        <div class="form-group row" id="message" style="display:none">
            <div class="col-md-12">
                <div class="alert-danger">
                    <?=Yii::t('app', 'The first invoice will have date in the next Month')?>
                </div>
            </div>
        </div>

        <?php if($action=='active') { ?>
        <div class="form-group field-contract-node-id required">
            <?php Html::label(Yii::t('westnet','Node'));
            
            echo $form->field($connection, 'node_id')->widget(Select2::class, [
                    'data' => $nodes,
                    'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false, 'id' => 'node_id'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]
            );?>
        </div>

        <?php
            if ($connection->access_point_id) {
                $data = [$connection->access_point_id => $connection->accessPoint->name];
            }else {
                $data = [];
            }

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
        
        ?>

        <?php echo $form->field($connection, 'mac_address')->textInput()?>


        <div class="form-group field-contract-second-ip required">
            <?php

            echo Html::label(Yii::t('westnet','ip4_2'));
            echo Html::dropDownList('Connection[use_second_ip]', $connection->use_second_ip, ['0'=>Yii::t('app', 'No'), '1'=> Yii::t('app', 'Yes')], [
                'class' => 'form-control'
            ] );
            ?>
            <div class="help-block"></div>
        </div>

        <div class="form-group field-contract-has_public_ip required">
            <?php

            echo Html::label(Yii::t('westnet','Has Public IP'));
            echo Html::dropDownList('Connection[has_public_ip]', $connection->has_public_ip, ['0'=>Yii::t('app', 'No'), '1'=> Yii::t('app', 'Yes')], [
                'class' => 'form-control',
                'id' => 'has_public_ip'
            ] );
            ?>
        </div>

        <div class="form-group field-contract-public_ip required">
            <?php
            echo Html::label(Yii::t('westnet','Public IP'));
            echo MaskedInput::widget([
                'model' => $connection,
                'name' => 'Connection[ip4_public]',
                'id' => 'connection_ip4_public',
                'clientOptions' => [
                    'alias' =>  'ip'
                ],
            ]);
            ?>
            <div class="help-block"></div>
        </div>
        <?php } ?>
        <?=  \yii\bootstrap\Html::checkbox('linkADS', false, ['id'=> 'linkADS'])?><label><?=Yii::t('app', 'Link with ADS')?></label>
        
        <div id="ads" style="display: none;">
            <p>Ingrese el codigo de cliente del ADS correspondiente</p>
            <?=$this->render('_findEmptyAds', ['model'=> $model, 'form'=> $form])?>
        </div>
        <br>
        <div class="form-group">
            <?= Html::a(Yii::t('app', ($action=='active' ? 'Activate' : 'Save' )), null, ['class' => 'btn btn-primary', 'id'=>'submit']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>

    var InvoiceForm = new function(){
        this.free_days = <?php echo $invoice_free_days; ?>

        this.init = function() {
            $(document).off('change', '#contract-from_date')
                .on('change', '#contract-from_date', function(){
                InvoiceForm.changeFromDate();
            });
            $('#message').hide();

            $(document).off('click', '#submit').on('click', '#submit', function(){
                InvoiceForm.submit();
            });

            $(document).off('change', '#has_public_ip')
                .on('change', '#has_public_ip', function(){
                InvoiceForm.hasPublicIp();
            });
            $(document).on('click', '#linkADS', function(){
                if ($('#linkADS').is(':checked')) {
                    $('#ads').show(0500);
                }else{
                    $('#ads').hide(0500);
                }
            });
            InvoiceForm.hasPublicIp();
        }

        this.submit = function(){
            if(confirm('<?=Yii::t('app', 'Are you sure you want to activate this contract?')?>')) {
                $('#form_invoice').submit();
            }
        }

        this.changeFromDate = function(){
            var from_date = $('#contract-from_date').kvDatepicker('getDate');
            $('#message').hide();
            if (InvoiceForm.free_days > 0 && InvoiceForm.free_days <= from_date.getDate() ) {
                $('#message').show();
            }
        }

        this.hasPublicIp = function(){
            if($('#has_public_ip').val()=='0') {
                $('.field-contract-public_ip').hide();
                $('#connection_ip4_public').val('');
            } else {
                $('.field-contract-public_ip').show();
            }
        }
    };
</script>
<?php $this->registerJs('InvoiceForm.init();');?>