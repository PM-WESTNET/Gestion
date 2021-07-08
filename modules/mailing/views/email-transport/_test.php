<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?> 
<div class="modal fade" id="modalTest" tabindex="-1" role="dialog" aria-labelledby="modalTestLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <?php echo \app\modules\mailing\MailingModule::t('Send Email Test') ?>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" id="email_transport_id" value=""/>
                    <div class="form-group">
                        <label for="recipient-name" class="form-control-label"><?php echo \app\modules\mailing\MailingModule::t('Email') ?>:</label>
                        <input type="text" class="form-control" id="email" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo \app\modules\mailing\MailingModule::t('Close') ?></button>
                <button id="submit-message" type="button" class="btn btn-primary"><?php echo \app\modules\mailing\MailingModule::t('Send message') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="message-dialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div id="message"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo \app\modules\mailing\MailingModule::t('Close') ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    var EmailTransportTest = new function () {
        var self = this;
        this.init = function () {
            $(document).on('show.bs.modal', '#modalTest', function (event) {
                var button = $(event.relatedTarget)
                var id = button.data('id')

                var modal = $(this)
                modal.find('.modal-body #email_transport_id').val(id)
            });

            $(document).off('click', '#submit-message').on('click', '#submit-message', function (evt) {
                evt.preventDefault();
                send();
            });

        }

        var send = function () {
            $.ajax({
                url: '<?php echo \yii\helpers\Url::to(['/mailing/email-transport/test']) ?>?id=' + $('#modalTest #email_transport_id').val(),
                data: {
                    email_to: $('#modalTest #email').val()
                },
                method: 'POST',
                dataType: 'json'
            }).done(function (data) {
                var message = (data.status == 'ok' ? '<?php echo \app\modules\mailing\MailingModule::t('Message sended succesfully') ?>' : data.message);
                $('#modalTest').modal('hide');
                $('#message-dialog').find('#message').html(message);
                $('#message-dialog').modal('show')
            });
        }
    };
</script>
<?php
$this->registerJs('EmailTransportTest.init()')?>