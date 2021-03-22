<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\partner\models\PartnerDistributionModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="partner-distribution-model-form">

    <?php $form = ActiveForm::begin(); ?>
    <input type="hidden" value="<?php echo $model->partner_distribution_model_id ?>" name="PartnerDistributionModel[partner_distribution_model_id]" class="form-control" id="partner_distribution_model_id">

    <?= app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php if(!$model->isNewRecord) { ?>
    <div class="panel panel-primary" id="panel_operation_type">
        <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-items" aria-expanded="true" aria-controls="panel-body-items">
            <h3 class="panel-title"><?= Yii::t('partner', 'Partners') ?></h3>
        </div>
        <div class="panel-body collapse in" id="panel-body-items" aria-expanded="true">
            <div class="row" id="form-partner">
            </div>
            <div class="row" id="form-partner-items">
            </div>
        </div>
    </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>

</div>
<script>
    var PartnerDistributionModel = new function(){
        this.init = function(){
            $(document)
                .off('click', "#partner-add")
                .on('click', "#partner-add", function(){
                    PartnerDistributionModel.addItem();
                });

            $(document)
                .off('click', ".deleteItem")
                .on('click', ".deleteItem", function(){
                    PartnerDistributionModel.deleteItem(this);
                });
            $(document)
                .off('click', ".updateItem")
                .on('click', ".updateItem", function(){
                    PartnerDistributionModel.editItem(this);
            });
            PartnerDistributionModel.loadItems();
            PartnerDistributionModel.addItem(true);

        }

        this.loadItems = function(){
            $.ajax({
                url: '<?php echo Url::toRoute(['/partner/partner-distribution-model/list-partner']) ?>&partner_distribution_model_id='+$('#partner_distribution_model_id').val(),
                method: 'GET',
                dataType: 'html',
                success: function(data) {
                    $('#form-partner-items').html(data);
                }
            });
        }

        this.addItem = function(){
            $.ajax({
                url: '<?php echo Url::toRoute(['/partner/partner-distribution-model/add-partner']) ?>&partner_distribution_model_id='+$('#partner_distribution_model_id').val(),
                method: 'POST',
                dataType: 'html',
                data: $('#partner-add-form').serializeArray(),
                success: function(data) {
                    $('#form-partner').html(data);
                    PartnerDistributionModel.loadItems();
                }
            });
        }

        this.editItem = function(element){
            $.ajax({
                url: $(element).data('url'),
                method: 'GET',
                dataType: 'html',
                success: function(data) {
                    $('#form-partner').html(data);
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
                        PartnerDistributionModel.loadItems();
                    }
                });
            }
        }
    }
</script>
<?php  $this->registerJs("PartnerDistributionModel.init();"); ?>
