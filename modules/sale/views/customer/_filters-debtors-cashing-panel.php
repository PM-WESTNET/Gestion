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
    
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($searchModel, 'customer_number')->textInput()?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($searchModel, 'name')->textInput()?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($searchModel, 'customer_status')->checkboxList(['enabled'=> 'Habilitado', 'disabled'=> 'Deshabilitado'])->label('Estado del Cliente')?>
        </div>
    </div>

    <div class="row hidden-print">
        <div class="col-sm-2">
            <?= $form->field($searchModel, 'toDate')->widget(DatePicker::class, [
                'language' => Yii::$app->language,
                'model' => $searchModel,
                'attribute' => 'date',
                'dateFormat' => 'dd-MM-yyyy',
                'options'=>[
                    'class'=>'form-control dates',
                    'id' => 'to-date'
                ]
            ]);
            ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($searchModel, 'activatedFrom')->widget(DatePicker::class, [
                'language' => Yii::$app->language,
                'model' => $searchModel,
                'attribute' => 'date',
                'dateFormat' => 'dd-MM-yyyy',
                'options'=>[
                    'class'=>'form-control dates',
                    'id' => 'activated-date'
                ]
            ]);
            ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($searchModel, 'debt_bills_from')->textInput(['placeholder' => Yii::t('app', 'Quantity')]); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($searchModel, 'debt_bills_to')->textInput(['placeholder' => Yii::t('app', 'Quantity')]); ?>
        </div>
    </div>
    <div class="row hidden-print">
        <div class="col-sm-4">
            <?= $form->field($searchModel, 'contract_status')->checkboxList(['active'=> 'Activo', 'inactive'=> 'Inactivo', 'low-process' =>Yii::t('app', 'Low-process')]) ?>
        </div>
        <div class="col-sm-4">
            <?=
                $form->field($searchModel, 'customer_class_id')->dropDownList(ArrayHelper::map(CustomerClass::find()->all(), 'customer_class_id', 'name'), ['prompt' => 'Todas las Categorias'])
            ?>
        </div>
        <div class="col-sm-4">
            <label><?= Yii::t('app', 'Amount due')?></label>
            <div class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon-usd "></span></div>
                <input type="text" id="customersearch-amount_due" class="form-control" name="CustomerSearch[amount_due]" >

            </div>
        </div>
    </div>
    <div class="row hidden-print">
        <div class="col-sm-12">
        <?=
            $form->field($searchModel, 'nodes')->widget(Select2::class, [
                'language' => 'es',
                'data' => \yii\helpers\ArrayHelper::map(Node::find()->all(), 'node_id', 'name'),
                'options' => [
                    'multiple' => true,
                    'placeholder' => Yii::t('app', 'Select an option...')
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        ?>
        </div>
    </div>
   
    <div class="row hidden-print">
        <div class="col-sm-12">
            <?= $form->field($searchModel, 'company_id')->dropDownList(ArrayHelper::map(Company::find()->all(), 'company_id', 'name'), ['prompt' => 'Todas las Empresas']) ?>
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