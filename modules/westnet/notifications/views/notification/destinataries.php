<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\modules\westnet\models\Node;
use app\modules\westnet\notifications\NotificationsModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Notification */

$this->title = NotificationsModule::t('app', 'Assign destinataries') . ' | ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->notification_id]];
$this->params['breadcrumbs'][] = NotificationsModule::t('app', 'Assign destinataries');
?>

<div class="notification-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Destinataries -->
    <div class="container">

        <?php $form = ActiveForm::begin(['options' => ['enctransport' => 'multipart/form-data']]); ?>

        <?=
        $form->field($model, 'node_id')->dropdownList(yii\helpers\ArrayHelper::map(Node::find()->all(), 'node_id', 'name'), [
            'encode' => false,
            'separator' => '<br/>',
            'prompt' => \app\modules\westnet\notifications\NotificationsModule::t('app', 'Select an option...'),
        ])
        ?>

        <div class="form-group">
        <?= Html::submitButton(NotificationsModule::t('app', 'Assign destinataries'), ['class' => 'btn btn-primary']) ?>
        </div>

<?php ActiveForm::end(); ?>

    </div>
    <!-- end Destinataries -->

</div>
