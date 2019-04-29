<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\westnet\notifications\models\Transport;
use app\modules\westnet\notifications\NotificationsModule;
use app\components\companies\CompanySelector;
use app\modules\mailing\MailingModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Notification */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notification-form">

    <?php $form = ActiveForm::begin(['options' => ['enctransport' => 'multipart/form-data']]); ?>

    <?= CompanySelector::widget(['attribute'=>'company_id', 'model' => $model]);?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
    
    <?=
    $form->field($model, 'transport_id')->dropdownList(ArrayHelper::map(Transport::getAllEnabled(), 'transport_id', 'name'), [
        'encode' => false,
        'separator' => '<br/>',
        'prompt' => NotificationsModule::t('app', 'Select an option...'),
    ])
    ?>
    <div class="form-group " id="field-email_transport_id" style="display: none;">
        <label class="control-label" for="email_transport_id"><?= MailingModule::t('Email Transport')  ?></label>
        <select id="notification-email_transport_id" class="form-control" name="Notification[email_transport_id]">
        </select>

        <div class="help-block"></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    var NotificationForm = new function(){
        var self = this;

        this.init = function(){
            $(document).off('change', "#notification-transport_id,#notification-company_id")
                .on('change', "#notification-transport_id,#notification-company_id", function(evt){
                evt.preventDefault();
                self.loadEmail();
            });
            self.loadEmail();
        }

        this.loadEmail = function(){
            var company_id = $('#notification-company_id').val();
            var transport_id = $('#notification-transport_id').val();

            $.ajax({
                url: '<?php echo \yii\helpers\Url::to(['/westnet/notifications/notification/find-email-transports'])  ?>',
                data: {
                    company_id: company_id,
                    transport_id: transport_id
                },
                method: 'POST',
                dataType: 'json'
            }).done(function(data){
                $('#field-email_transport_id').hide();
                if(data.status == 'ok') {
                    var select = $('#notification-email_transport_id');
                    select.html('');
                    if(!data.data.length){
                        select.append('<option><?php echo Yii::t('app', 'No results') ?></option>');
                    } else {
                        $.each(data.data, function(i, item){
                            select.append('<option value="'+item.email_transport_id+'">'+item.name+'</option>');
                        });

                    }
                    if(data.transport == 'email') {
                        $('#field-email_transport_id label').html('Email Transport');
                    } else if(data.transport == 'browser') {
                        $('#field-email_transport_id label').html('Layout');
                    }
                    $('#field-email_transport_id').show();
                }
            });
        }
    }
</script>
<?php $this->registerJs('NotificationForm.init()') ?>