<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use app\modules\sale\models\DocumentType;
use app\modules\sale\models\TaxCondition;
use app\modules\sale\models\CustomerClass;
use app\modules\sale\models\CustomerCategory;
use app\modules\zone\models\Zone;
use app\modules\accounting\models\Account;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Customer $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
  'modelClass' => Yii::t('app','Customer'),
]);
?>

<?php if(isset($model->customer_id)): ?>

    <?php
    //TODO: hacer esto de una forma menos primitiva
    $this->registerJs("parent.Bill.selectCustomer($model->customer_id)");
    ?>

<?php else: ?>

<div class="customer-create">

    <div class="customer-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->errorSummary($model) ?>

        <?= \app\components\companies\CompanySelector::widget(['model' => $model]); ?>

        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'lastname')->textInput(['maxlength' => 45]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <?= $form->field($model, 'tax_condition_id')->dropDownList(
                    ArrayHelper::map(TaxCondition::find()->orderBy(['name'=>SORT_ASC])->all(), 'tax_condition_id', 'name' )
                ,['id'=>'tax_condition']) ?>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'document_type_id')->dropDownList( ArrayHelper::map(DocumentType::find()->all(), 'document_type_id', 'name' ) ) ?>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'document_number')->textInput(['maxlength' => 45]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <?php
                 if(Yii::$app->params['class_customer_required'])
                    echo $form->field($model, 'customerClass')->label(Yii::t('app', 'Customer Class'))->dropDownList( ArrayHelper::map(CustomerClass::find()->all(), 'customer_class_id', 'name' ))
                ?>
            </div>
            <div class="col-sm-6">
                <?php
                 if(Yii::$app->params['category_customer_required'])
                    echo $form->field($model, 'customerCategory')->label(Yii::t('app', 'Customer Category'))->dropDownList( ArrayHelper::map(CustomerCategory::find()->all(), 'customer_category_id', 'name' ))
                ?>
            </div>
        </div>
        <?= 
        in_array('email', Yii::$app->params['embed-fields']) ?
        $form->field($model, 'email')->textInput(['maxlength' => 45]) : '' ?>

        <?= 
        in_array('sex', Yii::$app->params['embed-fields']) ?
        $form->field($model, 'sex')->dropDownList(['female'=>Yii::t('app','Female'),'male'=>Yii::t('app','Male')]) : '' ?>

        <?= 
        in_array('phone', Yii::$app->params['embed-fields']) ?
        $form->field($model, 'phone')->textInput(['maxlength' => 45]) : '' ?>

        <?= 
        in_array('address', Yii::$app->params['embed-fields']) ?
        $form->field($model, 'address')->textInput(['maxlength' => 255]) : '' ?>

        <?= Html::hiddenInput('Customer[status]', 'enabled') ?>
        
        <?php if (Yii::$app->getModule("accounting")) { ?>
        <div class="form-group field-provider-account">
            <?= $form->field($model, 'account_id')->widget(Select2::className(),[
                'data' => yii\helpers\ArrayHelper::map(Account::getForSelect(), 'account_id', 'name' ),
                'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ]);
            ?>
        </div>
        <?php } ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
<?php endif; ?>

<script>
    var Customer = new function(){
        
        var typeMap = <?= yii\helpers\Json::encode(
                ArrayHelper::map(TaxCondition::find()->all(), 'tax_condition_id', 'document_type_id')
            )
        ?>;
        
        this.init = function(){
            $('#tax_condition').on('change', function(e){
                onTaxConditionChange(e);
            })
        }
        
        function onTaxConditionChange(e){
            var typeRequired = $(e.target).val();
            
            //if(typeMap[typeRequired]){
            $('#document_type').val(typeMap[typeRequired]);
            //}
        }
        
    }
</script>

<?php $this->registerJs('Customer.init();') ?>