<?php

use app\modules\accounting\models\Account;
use app\modules\accounting\models\MoneyBoxType;
use app\modules\config\models\Config;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\MoneyBox */
/* @var $form yii\widgets\ActiveForm */
$money_box_type_bank = Config::getValue('money_box_bank');
?>

<div class="money-box-form">

    <?php $form = ActiveForm::begin(); ?>
    <input type="hidden" value="<?php echo $model->money_box_id ?>" name="MoneyBox[money_box_id]" class="form-control" id="moneybox-money_box_id">

    <?= $form->field($model, 'name')->textInput(['maxlength' => 150]) ?>

    <?= $form->field($model, 'money_box_type_id')->dropDownList( ArrayHelper::map( MoneyBoxType::find()->all(), "money_box_type_id", "name"), [
        'prompt' => Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('accounting', 'Money Box Type')]).'...',
        'id'=> 'money_box_type_id'] )  ?>

    <div class="form-group">
        <?= $form->field($model, 'account_id')->widget(Select2::className(),[
            'data' => yii\helpers\ArrayHelper::map(Account::getForSelect(), 'account_id', 'name' ),
            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]);
        ?>
    </div>

    <div class="panel panel-primary" id="panel_operation_type">
        <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-items" aria-expanded="true" aria-controls="panel-body-items">
            <h3 class="panel-title"><?= Yii::t('accounting', 'Operations Type') ?></h3>
        </div>
        <div class="panel-body collapse in" id="panel-body-items" aria-expanded="true">
            <div class="row" id="form-operation_type">
            </div>
            <div class="row" id="form-operation_type-items">
            </div>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    var MoneyBox = new function(){
        this.init = function(){
            $(document)
                .off('click', "#operation-type-add")
                .on('click', "#operation-type-add", function(){
                    MoneyBox.addItem();
                });

            $(document)
                .off('click', ".deleteItem")
                .on('click', ".deleteItem", function(){
                    MoneyBox.deleteItem(this);
                });
            $(document)
                .off('click', ".updateItem")
                .on('click', ".updateItem", function(){
                    MoneyBox.editItem(this);
            });
            $(document).off('change', '#money_box_type_id')
                .on('change', '#money_box_type_id', function(){
                MoneyBox.operationType($(this).val());
            });

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                MoneyBox.loadItems($(this).data('page'));
            });

            MoneyBox.loadItems();
            MoneyBox.addItem(true);
            MoneyBox.operationType($('#money_box_type_id').val());

        }

        this.operationType = function(value) {
            if(value==<?php echo ($money_box_type_bank)  ?>) {
                $('#panel_operation_type').show();
            } else  {
                $('#panel_operation_type').hide();
            }
        }

        this.loadItems = function(page){

            var url = '<?php echo Url::toRoute(['/accounting/money-box/list-operation-type']) ?>&money_box_id='+$('#moneybox-money_box_id').val();

            if (page !== undefined) {
                url = url + '&page=' + (parseInt(page) + 1);
            }


            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'html',
                success: function(data) {
                    $('#form-operation_type-items').html(data);
                }
            });
        }

        this.addItem = function(){
            $.ajax({
                url: '<?php echo Url::toRoute(['/accounting/money-box/add-operation-type']) ?>&money_box_id='+$('#moneybox-money_box_id').val(),
                method: 'POST',
                dataType: 'html',
                data: $('#operation-type-add-form').serializeArray(),
                success: function(data) {
                    $('#form-operation_type').html(data);
                    MoneyBox.loadItems();
                }
            });
        }

        this.editItem = function(element){
            $.ajax({
                url: $(element).data('url'),
                method: 'GET',
                dataType: 'html',
                success: function(data) {
                    $('#form-operation_type').html(data);
                    $('html,body').animate({
                        scrollTop: ($('#form-operation_type').top - 5)
                    }, 'slow')
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
                        MoneyBox.loadItems();
                    }
                });
            }
        }
    }
</script>
<?php  $this->registerJs("MoneyBox.init();"); ?>
