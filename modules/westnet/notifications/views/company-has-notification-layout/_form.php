<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\westnet\notifications\components\helpers\LayoutHelper;
/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\CompanyHasNotificationLayout */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-has-notification-layout-form">

    <?php $form = ActiveForm::begin(); ?>

    <!-- id="companyhasnotificationlayout-company_id" -->
    <?= app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

    <!-- change to getalias -->
    <!-- how to add a group-input using templates https://stackoverflow.com/questions/47140005/yii2-how-add-a-symbol-before-and-after-an-input-field -->
    <?= $form->field($model, 'layout_path',
        [
            'template' => '{label}<div class="input-group"><span class="input-group-addon">'.$model->layouts_base_path.'</span>{input}<span class="input-group-addon">.php</span></div>{error}{hint}'
        ])->dropDownList(LayoutHelper::getLayouts())
        ;
    ?>

    <?= $form->field($model, 'is_enabled')->checkBox() ?>
    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
