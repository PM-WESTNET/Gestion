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

$this->title = ($model->status=='draft' ? Yii::t('app', 'Active Contract') : Yii::t('app', 'Update') . " " . Yii::t('westnet', 'Connection') )  ." - " .
    $model->customer->name . " - " . Yii::t('app', 'Contract Number') .": " . $model->contract_id;
$this->params['breadcrumbs'][] = ['label' => $model->customer->name, 'url' => ['/sale/customer/view', 'id'=> $model->customer_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Contract Number') .": " . $model->contract_id;
$this->params['breadcrumbs'][] = Yii::t('westnet', 'Connection');
?>
<div class="contract-update">

    <h2><?= Html::encode($this->title) ?></h2>

    <?php $form = ActiveForm::begin([
        'id' => 'form_invoice'
    ]); ?>
    <div class="contract-form">
        <?= Html::hiddenInput('Contract[contract_id]', $model->contract_id) ?>
        <div class="form-group field-contract-node-id required">
            <?php Html::label(Yii::t('westnet','Node'));
            $query = Node::find();
            $query->select(['node.node_id', 'concat(node.name, \' - \', s.name) as name'])
                ->leftJoin('server s', 'node.server_id = s.server_id');

            if ($connection->access_point_id) {
                $data = [$connection->access_point_id => $connection->accessPoint->name];
            }else {
                $data = [];
            }
            echo $form->field($connection, 'node_id')->widget(Select2::className(), [
                    'data' => yii\helpers\ArrayHelper::map($query->all(), 'node_id', 'name' ),
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
            ?>
        </div>

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
                'value' => $connection->getIp4PublicFormatted(),
                'name' => 'Connection[ip4_public]',
                'id' => 'connection_ip4_public',
                'clientOptions' => [
                    'alias' =>  'ip'
                ],
            ]);
            ?>
            <div class="help-block"></div>
        </div>

        <div class="form-group">
            <?= Html::a(Yii::t('app', 'Update'  ), null, ['class' => 'btn btn-primary', 'id'=>'submit']); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>

    var InvoiceForm = new function(){

        this.init = function() {
            $(document).off('click', '#submit').on('click', '#submit', function(){
                InvoiceForm.submit();
            });

            $(document).off('change', '#has_public_ip')
                .on('change', '#has_public_ip', function(){
                InvoiceForm.hasPublicIp();
            });
            $("#connection_ip4_public").val( $("#connection_ip4_public").attr('value') );
            if( $("#connection_ip4_public").val()) {
                $("#has_public_ip").val(1);
            }
            InvoiceForm.hasPublicIp();
        }

        this.submit = function(){
            if(confirm('<?=Yii::t('westnet', 'Are you sure you want to update this connection?')?>')) {
                $('#form_invoice').submit();
            }
        }

        this.hasPublicIp = function(){
            $("#connection_ip4_public").val( $("#connection_ip4_public").attr('value') );

            if($('#has_public_ip').val()=='0') {
                $('.field-contract-public_ip').hide();
                $("#connection_ip4_public").attr('value-old', $("#connection_ip4_public").attr('value'));
                $('#connection_ip4_public').val('');
                $("#connection_ip4_public").attr('value', '');
            } else {
                $('.field-contract-public_ip').show();
                $("#connection_ip4_public").attr('value', $("#connection_ip4_public").attr('value-old'));
                $("#connection_ip4_public").val( $("#connection_ip4_public").attr('value') );
            }
        }
    };
</script>
<?php $this->registerJs('InvoiceForm.init();');?>