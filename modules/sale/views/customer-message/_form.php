<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerMessage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-message-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'status')->dropDownList([
            \app\modules\sale\models\CustomerMessage::STATUS_ENABLED => Yii::t('app','Enabled'),
            \app\modules\sale\models\CustomerMessage::STATUS_DISABLED => Yii::t('app','Disabled')
    ]) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6, 'id' => 'message']) ?>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title"><?php echo Yii::t('app','Available Fields')?></h5>
        </div>
        <div class="panel-body">
            <p style="font-weight: bold"><?php echo Yii::t('app','Use the following options for insert data in the message')?></p>
            <p>
                <?php foreach (\app\modules\sale\models\CustomerMessage::availablesFields() as $key => $field):?>
                    <?php echo Html::a($field['description'], '#', ['class' => 'btn btn-default btn-sm field-btn', 'data-template' => '{'.$key.'}'])?>
                <?php endforeach;?>
            </p>
        </div>
    </div>


    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    
    var CustomerMessage = new function() {
        
        this.init= function () {
            $(document).on('click', '.field-btn', function (e) {
                e.preventDefault();
                CustomerMessage.insertField($(this));
            })
        };
        
        this.insertField = function (field) {
            var txtarea = document.getElementById('message');
            var text = $(field).data('template');
            if (!txtarea) {
                return;
            }

            var scrollPos = txtarea.scrollTop;
            var strPos = 0;
            var br = ((txtarea.selectionStart || txtarea.selectionStart === '0') ?
                "ff" : (document.selection ? "ie" : false));
            if (br === "ie") {
                txtarea.focus();
                var range = document.selection.createRange();
                range.moveStart('character', -txtarea.value.length);
                strPos = range.text.length;
            } else if (br === "ff") {
                strPos = txtarea.selectionStart;
            }

            var front = (txtarea.value).substring(0, strPos);
            var back = (txtarea.value).substring(strPos, txtarea.value.length);
            txtarea.value = front + text + back;
            strPos = strPos + text.length;

            if (br == "ie") {
                txtarea.focus();
                var ieRange = document.selection.createRange();
                ieRange.moveStart('character', -txtarea.value.length);
                ieRange.moveStart('character', strPos);
                ieRange.moveEnd('character', 0);
                ieRange.select();
            } else if (br == "ff") {
                txtarea.selectionStart = strPos;
                txtarea.selectionEnd = strPos;
                txtarea.focus();
            }

            txtarea.scrollTop = scrollPos;
        }
    }
    
    
</script>

<?php $this->registerJs('CustomerMessage.init()')?>