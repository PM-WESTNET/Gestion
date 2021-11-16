<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\CustomerClass;
use kartik\widgets\Select2;
use app\modules\westnet\models\Node;
use app\modules\sale\models\Company;

?>

<div class="debtors-filters">

    <?php $form = ActiveForm::begin([
        'action' => [$action],
        'method' => 'get',
    ]); ?>
    

    <div class="row hidden-print">
        <div class="col-sm-3">
            <?= $form->field($searchModel, 'debt_bills_from')->textInput(['placeholder' => Yii::t('app', 'Quantity')]); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($searchModel, 'debt_bills_to')->textInput(['placeholder' => Yii::t('app', 'Quantity')]); ?>
        </div>
    </div>
    <div class="row hidden-print">
        <div class="col-sm-4">
            <?= $form->field($searchModel, 'contract_status')->checkboxList(['active'=> 'Activo', 'inactive'=> 'Inactivo', 'low-process' =>Yii::t('app', 'Low-process') ,'low' => Yii::t('app', 'Low')]) ?>
        </div>
        
    </div>
    <div class="row hidden-print">
        <div class="col-sm-12">
            <div class="form-group">
                <label class="control-label">&nbsp;</label>
                <div class="pull-right">
                    <?= Html::submitButton('<span class="glyphicon glyphicon-search"></span> ' .Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('<span class="glyphicon glyphicon-remove"></span> ' .Yii::t('app', 'Clear'), $form->action, ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>