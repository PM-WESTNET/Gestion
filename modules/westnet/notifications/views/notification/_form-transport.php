<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\westnet\notifications\models\Transport;
use app\modules\westnet\notifications\NotificationsModule;
use app\components\companies\CompanySelector;
use app\modules\mailing\MailingModule;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Notification */
/* @var $form yii\widgets\ActiveForm */
$integratech_transport = Transport::findOne(['slug' => 'sms-integratech']);
?>

<div class="notification-form">

    <?php $form = ActiveForm::begin(['options' => ['enctransport' => 'multipart/form-data']]); ?>

    <?= CompanySelector::widget(['attribute'=>'company_id', 'model' => $model]);?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'transport_id')->dropdownList(ArrayHelper::map(Transport::getAllEnabled(), 'transport_id', 'name'), [
        'encode' => false,
        'separator' => '<br/>',
        'prompt' => NotificationsModule::t('app', 'Select an option...'),
        'id' => 'transport'
    ])?>

    <div class="row" id="test-phone-fields">
        <div class="col-xs-6">
            <?= $form->field($model, 'test_phone')->textInput() ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'test_phone_frecuency')->textInput() ?>
        </div>
        <label style="color: grey">
            Se enviará un mensaje al telefono de prueba en la frecuencia indicada.
            Por defecto la frecuencia es 1000.
            Es decir, que cada mil mensajes enviados, se enviará un mensaje al teléfono de prueba
        </label>
    </div>



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
            $(document).off('change', "#transport,#notification-company_id")
                .on('change', "#transport,#notification-company_id", function(evt){
                evt.preventDefault();
                self.loadEmail();

                if($('#transport').val() == <?= $integratech_transport->transport_id ?>) {
                    $('#test-phone-fields').removeClass('hidden');
                } else {
                    $('#test-phone-fields').addClass('hidden');
                }
            });
            self.loadEmail();
            $('#test-phone-fields').addClass('hidden');

            /**$('#transport').on('change', function () {
                if($('#transport').val() == <?= $integratech_transport->transport_id ?>) {
                   $('#test-phone-fields').removeClass('hidden');
                } else {
                   $('#test-phone-fields').addClass('hidden');
                }
            })**/
        }

        this.loadEmail = function(){
            var company_id = $('#notification-company_id').val();
            var transport_id = $('#transport').val();

            $.ajax({
                url: '<?= Url::to(['/westnet/notifications/notification/find-email-transports'])  ?>',
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