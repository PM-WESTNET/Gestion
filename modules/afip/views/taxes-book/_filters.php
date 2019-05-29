<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\companies\CompanySelector;
use kartik\select2\Select2;
use app\modules\afip\models\TaxesBook;
use yii\jui\DatePicker;
?>

<div class="taxes-book-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]); ?>

    <div class="col-sm-12">
        <div class="col-sm-6">
            <?= CompanySelector::widget([
                    'attribute'=>'company_id',
                    'model' => $model,
                    'inputOptions' => [
                        'prompt' => Yii::t('app', 'All')
                    ]
            ])?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'status')->widget(Select2::class, [
                    'data' => TaxesBook::getStatusesForSelect(),
                    'pluginOptions' => [
                        'placeholder' => Yii::t('app', 'Select ...'),
                        'allowClear' => true,
                    ],
            ]) ?>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="col-sm-6">
            <?= $form->field($model, 'period')->widget(DatePicker::class, [
                    'language' => 'es-AR',
                    'dateFormat' => 'yyyy-MM',
                    'options' => [
                            'class' => 'form-control'
                    ]
            ])?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'number') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
