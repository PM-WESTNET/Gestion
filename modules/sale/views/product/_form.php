<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\bootstrap\Alert;
use yii\helpers\ArrayHelper;
use \app\modules\sale\models\Unit;
use app\modules\sale\models\Tax;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Product $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
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
        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>            
        </div>
        <div class="col-sm-6 col-xs-12">
            <?php
            if (Yii::$app->getModule('accounting')) {
            ?>
            <div class="form-group field-provider-account">
                <?= $form->field($model, 'account_id')->widget(Select2::className(),[
                    'data' => yii\helpers\ArrayHelper::map(app\modules\accounting\models\Account::getForSelect(), 'account_id', 'name' ),
                    'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]);
                ?>
            </div>
            <?php } ?>
        </div>

        <div class="col-xs-12">
            <?= $form->field($model, 'code')->textInput(['maxlength' => 45,'id'=>'code'])->hint(Yii::t('app','This is the unique identifier of the product. Generally, it is obtained from a barcode.')) ?>
        </div>

        <div class="col-xs-12">
            <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
        </div>

        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'unit_id')->dropDownList( ArrayHelper::map( Unit::find()->all(), 'unit_id', 'name' ) ) ?>
        </div>

        <div class="col-sm-6 col-xs-12">
            <?php if($model->isNewRecord && !Yii::$app->params['companies']['enabled']): ?>
            <?= $form->field($model, 'initial_stock')->textInput() ?>
            <?php endif; ?>
        </div>
        
        <?php if(app\modules\config\models\Config::getValue('enable_secondary_stock')): ?>
            <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'secondary_unit_id')->dropDownList( ArrayHelper::map( Unit::find()->all(), 'unit_id', 'name' ), ['prompt' => Yii::t('app', 'Select')] ) ?>
            </div>
        
            <div class="col-sm-6 col-xs-12">
            <?php if($model->isNewRecord && !Yii::$app->params['companies']['enabled']): ?>
                <?= $form->field($model, 'initial_secondary_stock')->textInput() ?>
            <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="col-xs-12">
            <?= $form->field($model, 'status')->dropDownList( ['enabled'=>Yii::t('app','Enabled'), 'disabled'=>Yii::t('app','Disabled')] ) ?>
        </div>
        
        <?php if (Yii::$app->getModule('accounting')): ?> 
        <div class="col-xs-12">
            <?= $form->field($model, 'product_commission_id')->dropDownList(app\modules\westnet\models\ProductCommission::findForSelect(),[
                'prompt' => ''
            ]) ?>
        </div>
        <?php endif; ?>
        
        <div class="col-xs-12">
            <?= $form->field($model, 'categories')->checkboxList(ArrayHelper::map(app\modules\sale\models\Category::getOrderedCategories(),'category_id','tabName'),['encode'=>false, 'separator'=>'<br/>']) ?>
        </div>

    </div>
    
    <hr/>
    
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::t('app','Price'); ?></h3>
            </div>

            <div class="panel-body">
                
                
                <div class="col-sm-3 col-xs-12">
                    <?php 
                    //Impuestos
                    foreach(Tax::find()->all() as $tax){

                        if($tax->required){
                            echo $form->field($model, 'taxRates')->dropDownList(ArrayHelper::map( $tax->taxRates, 'tax_rate_id', 'name' ), ['name' => 'Product[taxRates][]'])->label($tax->name); 
                        }else{
                            echo $form->field($model, 'taxRates')->dropDownList(ArrayHelper::map( $tax->taxRates, 'tax_rate_id', 'name' ), ['name' => 'Product[taxRates][]', 'prompt' => Yii::t('app','Select')])->label($tax->name); 
                        }
                        
                    }
                    ?>
                </div>
            
                <div class="col-sm-4 col-xs-12">
                    <?= $form->field($price, 'net_price')->textInput(['maxlength' => 20, 'id'=>'net_price']) ?>
                </div>

                <div class="col-sm-5 col-xs-12">
                    <label><?= $price->getAttributeLabel('exp_date'); ?></label>
                    <?php 
                    
                    echo yii\jui\DatePicker::widget([
                        'language' => Yii::$app->language,
                        'model' => $price,
                        'attribute' => 'exp_date',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control'
                        ]
                    ]);
                    ?>                        
                    <div class="hint-block"><?= Yii::t('app','Expiration date is used to remember you to update the price.') ?></div>
                </div>
            </div>
        </div>        
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app','Media'); ?></h3>
        </div>
        <div class="panel-body">
            <?= \app\modules\media\components\upload\UploadWidget::widget(['media' => $model->media]); ?>
        </div>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    
    <?php 
    //Modal para cargar codigo de barras
    $modal = Modal::begin([
        'header' => '<h2>'.$model->getAttributeLabel('code').'</h2>',
        'clientEvents'=> ['shown.bs.modal'=>'function(){$("#bar_code").focus();}'],
        'id'=>'m1',
    ]);
        
        //Mostramos el error del codigo
        foreach($model->getErrors('code') as $error)
            echo Alert::widget([
                'options' => [
                    'class' => 'alert-danger',
                ],
                'body' => $error
            ]);;
    
        echo '<div class="form-group">';
        echo '<label>'.Yii::t('app','Please, bring the reader to the barcode or type the code and press enter.').'</label>';
        echo Html::textInput('bar_code', '', ['id'=>'bar_code','class'=>'form-control']);
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>'.Yii::t('app','If you need a new code, please press the button "Generate new code".').'</label><br/>';
        echo '<a class="btn btn-primary" onclick="Code.generate();">'.Yii::t('app','Generate new code').'</a>&nbsp';
        echo '<a class="btn btn-warning" onclick="Code.cancel();">'.Yii::t('app','Cancel').'</a>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<div class="hint-block">'.Yii::t('app','Press "Space" to generate a new code.').'</div>';
        echo '<div class="hint-block">'.Yii::t('app','Press "Esc" to cancel.').'</div>';
        echo '<div class="hint-block">'.Yii::t('app','Press "Enter" if you type the code by hand.').'</div>';
        echo '</div>';
        
    Modal::end();

    ?>
    
    <script type="text/javascript">
    
    var Code = new function(){
        
        this.load = function(event){
            
            if(event.which == 13){
                
                if($("#bar_code").val().trim() == ''){
                    this.generate();
                }else{
                    $("#code").val( $("#bar_code").val() );
                    $('#m1').modal('hide');
                }

                //Borramos el codigo actual
                $("#bar_code").val('')
                
            }else if(event.which == 32){
                event.preventDefault();
                this.generate();
            }
        }
        
        this.input = function(){
            $('#m1').modal('show');
        }
        
        this.generate = function(){
            $("#code").val('AUTO');
            $('#m1').modal('hide');
        }
        
        this.cancel = function(){
            
            $('#m1').modal('hide');
            
        }
    }
    
    </script>
    
    <?php 
    //Evento que permite que el lector de codigos de barra ingrese el codigo
    $js = '$("#bar_code").on("keypress",function(event){Code.load(event);});';
    
    //Si es necesario modificar el codigo, al hacer clic en el mismo, lo podemos hacer
    $js .= '$("#code").on("click",function(){Code.input();});';
    
    //Si la app esta configurada para que se deba introducir primero el codigo del producto y luego los demas datos:
    if($model->isNewRecord)
        if(Yii::$app->params['code_first_on_create']) $js .= '$("#m1").modal("show");';
    
    $this->registerJs($js);
    ?>
    
</div>
