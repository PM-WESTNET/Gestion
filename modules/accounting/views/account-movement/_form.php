<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountMovement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="account-movement-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
        echo Html::activeHiddenInput($model, 'account_movement_id', ['value'=>$model->account_movement_id]);
        echo Html::activeHiddenInput($model, 'accounting_period_id', ['value'=>\app\modules\accounting\models\AccountingPeriod::getActivePeriod()->accounting_period_id]);
        echo Html::activeHiddenInput($model, 'status', ['value'=>\app\modules\accounting\models\AccountMovement::STATE_DRAFT]);
    ?>

    <?php echo app\components\companies\CompanySelector::widget(['model'=>$model]); ?>
    
    <?php
        echo $this->render('@app/modules/partner/views/partner-distribution-model/_selector', ['model' => $model, 'form'=>$form]);
    ?>

    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => 150]) ?>

    <?php if ($itemsDataProvider!==null): ?>

    <div class="panel panel-primary">
        <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-items" aria-expanded="true" aria-controls="panel-body-items">
            <h3 class="panel-title"><?= Yii::t('app', 'Items') ?></h3>
        </div>
        <div class="panel-body collapse in" id="panel-body-items" aria-expanded="true">
            <div class="row" id="form-items">
            </div>
            <div class="row" id="form-list-items">
            </div>
        </div>
    </div>
    
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    var AccountMovement = new function(){
        this.init = function(){
            $(document)
                .off('click', "#item-add")
                .on('click', "#item-add", function(){
                    AccountMovement.addItem();
            });

            $(document)
                .off('click', ".deleteItem")
                .on('click', ".deleteItem", function(){
                    AccountMovement.deleteItem(this);
            });
            $(document)
                .off('click', ".updateItem")
                .on('click', ".updateItem", function(){
                   AccountMovement.editItem(this);
            });

            AccountMovement.loadItems();
            AccountMovement.addItem(true);
        }

        this.loadItems = function(){
            $.ajax({
                url: '<?php echo Url::toRoute(['/accounting/account-movement/list-items']) ?>&account_movement_id='+$('#accountmovement-account_movement_id').val(),
                method: 'GET',
                dataType: 'html',
                success: function(data) {
                    $('#form-list-items').html(data);
                }
            });
        }

        this.addItem = function(addFirst){
            if( ((new Number($('#accountmovementitem-debit').val())) != 0 && (new Number($('#accountmovementitem-credit').val())) != 0 ) &&
                !addFirst
            ) {
                $('.field-accountmovementitem-debit').addClass('has-error');
                $('.field-accountmovementitem-credit').addClass('has-error');
                $('.field-accountmovementitem-debit .help-block').text('<?php echo Yii::t('accounting', 'Just add debit or credit, no both of them') ?>');
            } else {
                $('.field-accountmovementitem-debit').removeClass('has-error');
                $('.field-accountmovementitem-credit').removeClass('has-error');
                $('.field-accountmovementitem-debit .help-block').text('');
                $.ajax({
                    url: '<?php echo Url::toRoute(['/accounting/account-movement/add-item']) ?>&account_movement_id='+$('#accountmovement-account_movement_id').val(),
                    method: 'POST',
                    dataType: 'html',
                    data: $('#item-add-form').serializeArray(),
                    success: function(data) {
                        $('#form-items').html(data);
                        AccountMovement.loadItems();
                    }
                });
            }
        }

        this.editItem = function(element){

            $.ajax({
                url: $(element).data('url'),
                method: 'GET',
                dataType: 'html',
                success: function(data) {
                    $('#form-items').html(data);
                    $("#item-add").text("<?php echo Yii::t('app', 'Update') ?>");
                    $("#item-add").switchClass("btn-success","btn-primary");
                }
            });
        }

        this.deleteItem = function(element){
            if(confirm('<?php echo Yii::t('yii','Are you sure you want to delete this item?') ?>')) {
                $.ajax({
                    url: $(element).data('url'),
                    method: 'POST',
                    dataType: 'html',
                    success: function(data) {
                        $('#form-list-items').html(data);
                    }
                });
            }
        }
    }
</script>
<?php  $this->registerJs("AccountMovement.init();"); ?>
