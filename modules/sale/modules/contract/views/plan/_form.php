<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \app\modules\sale\modules\contract\models\PlanFeature;
use app\modules\sale\models\Unit;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\Tax;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\Plan */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="plan-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-12 noPadding">
            <?php
            echo $form->field($model, 'company_id')
                ->label(Yii::t('app', 'Company'))
                ->dropDownList( ArrayHelper::map(\app\modules\sale\models\Company::find()->andWhere(['status'=>'enabled', 'parent_id'=>null])->all(), 'company_id', 'name' ), [
                    'prompt' => Yii::t('app','All'),
                    'id' => 'company_id'
                ] ) ?>
        </div>

    </div>


    <div class="row">
        <div class="col-md-6 noPadding">
            <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>
        </div>
        <div class="col-md-6 no-padding-right">
            <?= $form->field($model, 'system')->textInput(['maxlength' => 45]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 noPadding">
            <?= $form->field($model, 'code')->textInput(['maxlength' => 45]) ?>
        </div>
        <div class="col-md-4 no-padding-right">
            <div class="form-group field-plan-code">
                <label class="control-label" for="show_in_ads"><?php echo Yii::t('app', 'Show In Ads') ?></label>
                <?= $form->field($model, 'show_in_ads')->checkbox(['label'=>false])->label(false) ?>
                <div class="help-block"></div>
            </div>


        </div>
        <div class="col-md-4 no-padding-right" id="ads-name" style="display: <?php echo ($model->show_in_ads ? 'block' : 'none' ) ?>">
            <?= $form->field($model, 'ads_name')->textInput(['maxlength' => 15])->label(Yii::t('app', 'Ads Name')) ?>
        </div>
    </div>




    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enabled' => 'Enabled', 'disabled' => 'Disabled', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'unit_id')->dropDownList( ArrayHelper::map( Unit::find()->all(), 'unit_id', 'name' ) ) ?>
    
    <?= $form->field($model, 'categories')->checkboxList(yii\helpers\ArrayHelper::map(app\modules\sale\models\Category::getOrderedCategories(),'category_id','tabName'),['encode'=>false, 'separator'=>'<br/>'])?>

    <?php
        $plan_features_parent= PlanFeature::find()->where('parent_id IS NULL')->all();
        //$i=0;

        foreach ($plan_features_parent as $plan_feature_parent) {
            if(empty($plan_feature_parent->planFeatures)){
                    //echo $form->field($model, '_planfeature['. $i .']')->checkbox(['label'=>$plan_feature_parent->name, 'value'=>$plan_feature_parent->plan_feature_id]);
            }
            else {

                $plan_features_child= PlanFeature::find()->where(['parent_id'=>$plan_feature_parent->plan_feature_id])->all();
                if($plan_feature_parent->type==='radiobutton'){
                    echo $form->field($model, '_planfeature['. $plan_feature_parent->plan_feature_id .']')->label($plan_feature_parent->name)->radioList(yii\helpers\ArrayHelper::map($plan_features_child,'plan_feature_id','tabName'),['encode'=>false, 'separator'=>'<br/>']);
                }
                else{
                    echo $form->field($model, '_planfeature['. $plan_feature_parent->plan_feature_id .']')->label($plan_feature_parent->name)->checkboxList(yii\helpers\ArrayHelper::map($plan_features_child,'plan_feature_id','tabName'),['encode'=>false,'separator'=>'<br/>']);
                }
            }
           // $i=$i+1;
        }

    ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app','Price'); ?></h3>
        </div>
        <div class="panel-body">
            
            <div class="row">
                <div class="col-xs-12">
                    <?php 
                    //Impuestos
                    foreach(Tax::find()->all() as $tax){

                        if($tax->required){
                            echo $form->field($model, 'taxRates')->dropDownList(ArrayHelper::map( $tax->taxRates, 'tax_rate_id', 'name' ), ['name' => 'Plan[taxRates][]'])->label($tax->name); 
                        }else{
                            echo $form->field($model, 'taxRates')->dropDownList(ArrayHelper::map( $tax->taxRates, 'tax_rate_id', 'name' ), ['name' => 'Plan[taxRates][]', 'prompt' => Yii::t('app','Select')])->label($tax->name); 
                        }
                        
                    }
                    ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($price, 'net_price')->textInput(['maxlength' => 20, 'id'=>'net_price']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <label><?= $price->getAttributeLabel('exp_date'); ?></label>
                    <?= DatePicker::widget([
                        'language' => 'es',
                        'model' => $price,
                        'attribute' => 'exp_date',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control'
                        ]
                    ]);
                    ?>
                    <div class="hint-block"><?= Yii::t('app','Expiration date is used to remember you to update the price.') ?></div>
                    <br>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($price, 'future_final_price')->textInput(['maxlength' => 20]) ?>
                    <div class="hint-block"><?= Yii::t('app','Future final price is used to report to the customers the next plan price via notifications.') ?></div>
                    <br>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <label><?= Html::activeCheckbox($model, 'big_plan', ['class' => 'agreement']) ?></label>
        </div>
        <hr>
        <br>
    </div>
    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    var Plan = new function() {
        var self = this;

        this.init = function(){
            $(document).off("click", "#plan-show_in_ads").on("click", "#plan-show_in_ads", function(evt){
                self.nameAds($(this).is(':checked'));
            });
        }

        this.nameAds = function(checked) {
            if(checked) {
                $("#ads-name").show();
            } else {
                $("#ads-name").hide();
            }
        }
    }
</script>
<?php $this->registerJs('Plan.init()') ?>