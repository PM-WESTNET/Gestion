<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/05/16
 * Time: 15:59
 */
use app\modules\partner\models\PartnerDistributionModel;

?>

<?php

    if(empty($model->partner_distribution_model_id)) {
        $pdm = [];
    } else {
        $pdm = \yii\helpers\ArrayHelper::map(PartnerDistributionModel::findAll(['company_id'=> $model->company_id]), 'partner_distribution_model_id', 'name' );
    }

    echo $form->field($model, 'partner_distribution_model_id')->dropDownList($pdm,[
        'prompt' => 'Select',
        'id'=>'partner_distribution_model_id']
    )->label(Yii::t('partner', 'Partner Distribution Model'))  ?>

<script>
    var PartnerDistributionModelSelector = new function(){
        this.init = function() {
            $(document).off('change', 'select[id$="company_id"]')
            .on('change', 'select[id$="company_id"]', function() {
                PartnerDistributionModelSelector.load();
            });
            <?php if (empty($model->partner_distribution_model_id)):?>
            PartnerDistributionModelSelector.load();
            <?php endif;?>
        }

        this.load = function(){
            $.get( "<?php echo yii\helpers\Url::toRoute('/partner/partner-distribution-model/get-by-company') ."&company_id="?>" + $('select[id$="company_id"]').val())
                .done(function(data){
                    var $select = $('#partner_distribution_model_id');
                    $select.find('option').remove();
                    $.each(data, function(key,item) {
                        $('<option>').val(item.partner_distribution_model_id).text(item.name).appendTo($select);
                    });
            });
        }
    }
</script>
<?php $this->registerJs('PartnerDistributionModelSelector.init();'); ?>