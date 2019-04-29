<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\config\models\Config;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\StockMovement $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="stock-movement-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

    <?= $form->field($model, 'type')->radioList(['in'=>Yii::t('app','In'),'out'=>Yii::t('app','Out')]) ?>

    <div class="row">
        <div class="col-sm-3">
            
            <?= $form->field($model, 'qty')->widget(app\modules\sale\components\StockInput::className(),[
                'unit' => $model->product->unit
            ]) ?>

            <?php if(Config::getValue('enable_secondary_stock') && $model->product->secondaryUnit): ?>
                <?= $form->field($model, 'secondary_qty')->widget(app\modules\sale\components\StockInput::className(),[
                    'unit' => $model->product->secondaryUnit
                ]) ?>
            <?php endif; ?>
            
        </div>
    </div>
    
    <?= $form->field($model, 'concept')->textInput(['maxlength' => 255]) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
