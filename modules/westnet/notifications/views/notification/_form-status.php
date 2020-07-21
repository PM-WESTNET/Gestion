<?php

use app\modules\westnet\notifications\models\Notification;
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
            <button type="submit" class="btn btn-primary btn-lg" name="Notification[status]" value="<?= ($model->status == Notification::STATUS_ENABLED || $model->status == Notification::STATUS_PENDING) ? Notification::STATUS_DISABLED : Notification::STATUS_ENABLED ?>">
                <span class="glyphicon glyphicon-ok"></span>
                <?php
                if($model->status == Notification::STATUS_ENABLED || $model->status == Notification::STATUS_PENDING){
                    echo NotificationsModule::t('app', 'Desactivate');
                }else{
                    echo NotificationsModule::t('app', 'Activate');
                }
                ?>
            </button>
        </div>
    </div>

<?php ActiveForm::end(); ?>