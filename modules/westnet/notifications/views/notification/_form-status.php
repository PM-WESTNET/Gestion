<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\tinymce\TinyMce;
use app\modules\westnet\notifications\NotificationsModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Notification */
/* @var $form yii\widgets\ActiveForm */
?>

<!--  Notification form -->
<?php $form = ActiveForm::begin(['id'=>'status-form']); ?>

    <?= $form->errorSummary($model) ?>

    <div class="row" style="margin: 50px;">
        <div class="col-sm-2 col-sm-offset-3">
            <a class="btn btn-danger btn-lg" href="<?= yii\helpers\Url::toRoute(['notification/view', 'id'=>$model->notification_id]) ?>"> 
                <span class="glyphicon glyphicon-remove"></span>
                <?= Yii::t('app', 'Cancel') ?> 
            </a>
        </div>
        <p class="visible-xs"></p>
        <div class="col-sm-2 col-sm-offset-2">
            <button type="submit" class="btn btn-primary btn-lg" name="Notification[status]" value="<?= $model->status != 'enabled' ? 'enabled' : 'disabled' ?>">
                <span class="glyphicon glyphicon-ok"></span>
                <?php
                if($model->status != 'enabled'){
                    echo NotificationsModule::t('app', 'Activate');
                }else{
                    echo NotificationsModule::t('app', 'Desactivate');
                }
                ?>
            </button>
        </div>
    </div>

<?php ActiveForm::end(); ?>