<?php

use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerCategory;
use app\modules\sale\models\CustomerClass;
use app\modules\sale\models\Product;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\models\Node;
use app\modules\westnet\notifications\models\Destinatary;
use app\modules\westnet\notifications\NotificationsModule;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Destinatary */
/* @var $form ActiveForm */
//var_dump($model);die();
?>

<div class="destinatary-form">

    <?php $form = ActiveForm::begin(['id'=>'mainForm']); ?>

    <?= $form->field($model, 'type')->dropDownList([
        'by_filters' => NotificationsModule::t('app','By filters'),
        'by_customers' => NotificationsModule::t('app','By customers'),
        'by_csv' => 'Importar ADS por CSV'
    ], ['id' => 'type']) ?>
    
    <div id="types">
        <div id="by_filters" style="display: none;">

            <?=
            $form->field($model, '_nodes')->widget(Select2::classname(), [
                'language' => 'es',
                'data' => ArrayHelper::map(Node::find()->orderBy('name')->all(), 'node_id', 'name'),
                'options' => [
                    'multiple' => true,
                    'placeholder' => NotificationsModule::t('app', 'Select an option...')
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>

            <?=
            $form->field($model, '_companies')->widget(Select2::classname(), [
                'language' => 'es',
                'data' => ArrayHelper::map(Company::find()->orderBy('name')->all(), 'company_id', 'name'),
                'options' => [
                    'multiple' => true,
                    'placeholder' => NotificationsModule::t('app', 'Select an option...')
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(NotificationsModule::t('app', 'Company'));;
            ?>

            <?php
            $contract_statuses=[];
            foreach (Contract::getStatusRange() as $sta => $label) {
                $contract_statuses[]= ['status' => $sta, 'label'=> $label];
            }
            echo $form->field($model, '_contract_statuses')->widget(Select2::classname(), [
                'language' => 'es',
                'data' => ArrayHelper::map($contract_statuses, 'status', 'label'),
                'options' => [
                    'multiple' => true,
                    'placeholder' => NotificationsModule::t('app', 'Select an option...')
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(NotificationsModule::t('app', 'Contract status'));
            ?>
            
            <?=
            $form->field($model, '_customer_class')->widget(Select2::classname(), [
                'language' => 'es',
                'data' => ArrayHelper::map(CustomerCategory::find()->orderBy('name')->all(), 'customer_category_id', 'name'),
                'options' => [
                    'multiple' => true,
                    'placeholder' => NotificationsModule::t('app', 'Select an option...')
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(NotificationsModule::t('app', 'Customer class'));
            ?>
            <?=
            $form->field($model, '_customer_categories')->widget(Select2::classname(), [
                'language' => 'es',
                'data' => ArrayHelper::map(CustomerClass::find()->orderBy('name')->all(), 'customer_class_id', 'name'),
                'options' => [
                    'multiple' => true,
                    'placeholder' => NotificationsModule::t('app', 'Select an option...')
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(NotificationsModule::t('app', 'Customer Category'));
            ?> 
            
            <?= $form->field($model, '_plans')->widget(Select2::classname(), [
                'language' => 'es',
                'data' => ArrayHelper::map(Product::find()->orderBy('name')->where(['type' => 'plan', 'status' => 'enabled'])->all(), 'product_id', 'name'),
                'options' => [
                    'multiple' => true,
                    'placeholder' => NotificationsModule::t('app', 'Select an option...')
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(NotificationsModule::t('app', 'Plans'));
            ?>

            <div class="row toClear">
                <div class="col-lg-6 no-padding-left">
                    <?= $form->field($model, 'contract_min_age') ?>
                </div>

                <div class="col-lg-6 no-padding-right">
                    <?= $form->field($model, 'contract_max_age') ?>
                </div>
            </div>

            <div class="row toClear">

                <div class="col-lg-6 no-padding-left">
                    <?=
                    $form->field($model, 'overdue_bills_from')->textInput([
                        'placeholder' => NotificationsModule::t('app', '0')
                    ])
                    ?>
                </div>

                <div class="col-lg-6 no-padding-right">
                    <?=
                    $form->field($model, 'overdue_bills_to')->textInput([
                        'placeholder' => NotificationsModule::t('app', '10')
                    ])
                    ?>
                </div>

            </div>

            <div class="row toClear">

                <div class="col-lg-6 no-padding-left">
                    <?=
                    $form->field($model, 'debt_from')->textInput([
                        'placeholder' => NotificationsModule::t('app', '0')
                    ])
                    ?>
                </div>

                <div class="col-lg-6 no-padding-right">
                    <?=
                    $form->field($model, 'debt_to')->textInput([
                        'placeholder' => NotificationsModule::t('app', '100000')
                    ])
                    ?>
                </div>

            </div>

            <?php if ($notification->transport->slug !== 'mobile-push'):?>
                <div class="row toClear">
                    <div class="col-lg-6 no-padding-left">
                        <?php echo $form->field($model, 'has_app')->radioList(['installed' => NotificationsModule::t('app','Installed'), 'not_installed' => NotificationsModule::t('app','Not Installed')])->label('¿Tiene instalada la App?')?>
                    </div>
                    <div class="col-lg-6 no-padding-left">
                        <?= 
                       // echo $form->field($model, 'has_automatic_debit')->radioList(['1' => 'Si', '0' => 'No' ])->label('¿Tiene débito automático?')
                        $form->field($model, 'has_automatic_debit')->widget(Select2::classname(), [
                            'language' => 'es',
                            'data' =>['1' => 'Si', '0' => 'No'],
                            'options' => [
                                'placeholder' => NotificationsModule::t('app', 'Select an option...')
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                        ?>
                    </div>
                </div>
            <?php endif;?>
        </div>

        <div id="by_csv" style="display: none;">
            <?php
            echo "*ADS separados por comas</br></br>";
            
            echo $form->field($model, 'ads_csv')->textarea(['rows' => '6']);
            

            ?>
        </div>

        <div id="by_customers" style="display: none;">

            <?php
            $values = [];
            foreach($model->customers as $customer){
                $values[$customer->customer_id] = $customer->fullName;
            }

            echo "Buscador</br>";
            echo Select2::widget([
                'name' => 'Destinatary[customers]',
                'value' => array_keys($values),
                'initValueText' => $values,
                'options' => [
                    'multiple' => true,
                    'placeholder' => 'Buscar Clientes'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => Url::to(['/westnet/notifications/destinatary/find-by-name']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {name:params.term, company_id: '.$model->notification->company_id .'}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(customer) { return customer.text; }'),
                    'templateSelection' => new JsExpression('function (customer) { return customer.text; }'),
                    'cache' => true
                ],
            ]); 
            ?>
            <hr/>
        </div>
        
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<style>
    .customer-list{
        max-height: 80vh;
        overflow: auto;
    }
    #destinatary-_nodes label{
        padding: 7.5px;
    }
    #destinatary-_nodes label:first-of-type{
        padding-left: 0;
    }
</style>

<script>
    
    var Destinatary = new function(){
        
        this.init = function(){
            $('#type').on('change', function(e){
                var val = $(this).val();
                $('#types > div').not('#'+val).hide(250);
                $('#'+val).show(250);
                Destinatary.resetForm();
            });
            var val = $('#type').val();
            $('#types > div').not('#'+val).hide(250);
            $('#'+val).show(250);
        }

        this.resetForm = function(){
            $("#mainForm .toClear input").val("")
            $("select.form-control.select2-hidden-accessible").val("");
            $("select.form-control.select2-hidden-accessible").trigger('change.select2');
        }

    }
    
</script>
<?php $this->registerJs('Destinatary.init();') ?>