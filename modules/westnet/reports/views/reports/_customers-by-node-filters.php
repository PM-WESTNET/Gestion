<?php

use app\modules\sale\models\Company;
use app\modules\sale\models\CustomerCategory;
use app\modules\sale\models\CustomerClass;
use app\modules\westnet\models\Node;
use app\modules\zone\models\Zone;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\modules\sale\models\Product;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\models\Customer;

/**
 * @var \yii\web\View $this
 */

$model = new \app\modules\sale\models\search\CustomerSearch();
$model->load(Yii::$app->request->getQueryParams())
?>

<div class="filters-costumer">


    <?php $form = ActiveForm::begin(['method'=> 'get', 'id' => 'filterForm', 'action' => ['customers-by-node']]) ?>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'company_id')->dropDownList(ArrayHelper::map(Company::find()->all(), 'company_id', 'name'), ['prompt' => 'Todas las Empresas', 'id' => 'company_id']) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'customer_category_id')->dropDownList(ArrayHelper::map(CustomerCategory::find()->all(), 'customer_category_id', 'name'), [
                    'prompt' => 'Todos los Rubros', 'id' => 'customer_category_id'
            ]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'customer_class_id')->dropDownList(ArrayHelper::map(CustomerClass::find()->all(), 'customer_class_id', 'name'), [
                    'prompt' => 'Todas las Categorias', 'id' => 'customer_class_id'
            ])?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $this->render('../../../../sale/views/customer/_find-zone-with-autocomplete', ['model' => $model, 'form' => $form]) ?>
        </div>

        <div class="col-sm-4">
            <?= $form->field($model, 'node_id')->widget(Select2::class, [
                'data' =>  ArrayHelper::map(Node::find()->orderBy('name')->all(), 'node_id', 'name'),
                'options' => ['placeholder' => Yii::t('app','All nodes'), 'multiple' => true],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ])?>
        </div>

        <div class="col-sm-4">
            <?= $form->field($model, 'plan_id')->widget(Select2::class, [
               'data' => ArrayHelper::map(Product::find()->where(['type'=>'plan', 'status' => 'enabled' ])->orderBy('name')->all(),'product_id', 'name'),
               'options' => ['placeholder' => Yii::t('app','Select an option...'), 'multiple' => true, 'id' => 'plan_id'],
               'pluginOptions' => [
                    'allowClear' => true
               ]
            ])
            ?>
        </div>

    </div>

    <!-- Checkboxes -->
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'customer_status')->checkboxList(['enabled'=> 'Habilitado', 'disabled' => 'Deshabilitado'], ['id' => 'customer_status']) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'connection_status')->checkboxList(['enabled'=> 'Habilitada', 'disabled' => 'Deshabilitada', 'forced' => 'Forzada', 'defaulter'=> Yii::t('app', 'Defaulter Connection'), 'clipped' => Yii::t('app', 'Clipped Connection'), 'low' => Yii::t('app', 'Low Account')], ['id' => 'connection_status']) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'contract_status')->checkboxList(Contract::getStatusesForSelect(), ['id' => 'contract_status'])?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'email_status')->checkboxList(Customer::getStatusEmailForSelect(), ['id' => 'email_status']) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'email2_status')->checkboxList(Customer::getStatusEmailForSelect(), ['id' => 'email2_status']) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'mobile_app_status')->checkboxList(['uninstalled' => 'Desintalada', 'installed' => 'Instalada'], ['id' => 'mobile_app_status']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-1 ">
            <?=  Html::submitInput('Filtrar', ['class'=> 'btn btn-primary', 'id'=> 'filterButton'])?>
        </div>
        <div class="col-sm-2">
            <?= Html::a('Borrar Filtros', Url::to(['customers-by-node']), ['class' => 'btn btn-default'])?>
        </div>
        <div class="col-sm-1 ">
            <button type="submit" class="btn btn-success" value = "btn-export" name = "button">Exportar</button>
        </div>
    </div>

    <?php ActiveForm::end();?>

</div>

<script>

    var CustomerSearch= new function(){

        this.init= function(){
            //Recreo la url para filtrar eliminando los campos ocultos que son
            //los que hacen que la url vaya incrementando de tama??o y produzca el error cuando la url
            //llega al tama??o m??ximo permitido.
            $(document).on('click', '#filterButton', function(e){
                e.preventDefault();
                var customerStatus = '';
                var connectionStatus = '';
                var contractStatus = '';
                var emailStatus = '';
                var email2Status = '';
                var mobileAppStatus = '';

                // Para los campos del form que son checkboxList recorro cada checkbox y si esta checkeado agrego el valor a la url
                $.each($('#customer_status input'), function(i, c){
                    if ($(c).is(':checked')) {
                        customerStatus = customerStatus + '&CustomerSearch%5Bcustomer_status%5D%5B%5D=' + $(c).val();
                    }
                });

                $.each($('#connection_status input'), function(i, c){
                    if ($(c).is(':checked')) {
                        connectionStatus = connectionStatus + '&CustomerSearch%5Bconnection_status%5D%5B%5DD=' + $(c).val();
                    }
                });

                $.each($('#contract_status input'), function(i, c){
                    if ($(c).is(':checked')) {
                        contractStatus = contractStatus + '&CustomerSearch%5Bcontract_status%5D%5B%5D=' + $(c).val();
                    }
                });

                $.each($('#email_status input'), function(i, c){
                    if ($(c).is(':checked')) {
                        emailStatus = emailStatus + '&CustomerSearch%5Bemail_status%5D%5B%5D=' + $(c).val();
                    }
                });

                $.each($('#email2_status input'), function(i, c){
                    if ($(c).is(':checked')) {
                        email2Status = email2Status + '&CustomerSearch%5Bemail2_status%5D%5B%5D=' + $(c).val();
                    }
                });

                $.each($('#mobile_app_status input'), function(i, c){
                    if ($(c).is(':checked')) {
                        mobileAppStatus = mobileAppStatus + '&CustomerSearch%5Bmobile_app_status%5D%5B%5D=' + $(c).val();
                    }
                });

                // Si en los campos de checkboxList no hay ningun checkbox seleccionado, agrego los campos vac??os
                if (customerStatus === '') {
                    customerStatus= '&CustomerSearch%5Bcustomer_status%5D=';
                }

                if (connectionStatus === '') {
                    connectionStatus= '&CustomerSearch%5Bconnection_status%5D=';
                }

                if (contractStatus === '') {
                    contractStatus= '&CustomerSearch%5Bcontract_status%5D=';
                }

                if (emailStatus === '') {
                    emailStatus = '&CustomerSearch%5Bemail_status%5D=';
                }

                if (email2Status === '') {
                    email2Status = '&CustomerSearch%5Bemail2_status%5D=';
                }

                if (email2Status === '') {
                    mobileAppStatus = '&CustomerSearch%5BmobileAppStatus%5D=';
                }

                // Remuevo todos los campos ocultos del formulario, esto campos se generan por cada vez que se filtra pero los
                //del filtro anterior no se eliminan y es eso lo que produce que la url crezca
                //$('CustomerSearch[customer_id]').remove();
                $('CustomerSearch[company_id]').remove();
                $('CustomerSearch[zone_id]').remove();
                $('CustomerSearch[customer_category_id]').remove();
                $('CustomerSearch[customer_class_id]').remove();
                $('CustomerSearch[node_id]').remove();
                $('CustomerSearch[plan_id]').remove();
                $('CustomerSearch[customer_status]').remove();
                $('CustomerSearch[connection_status]').remove();
                $('CustomerSearch[contract_status]').remove();
                //$('CustomerSearch[document_number]').remove();
                $('CustomerSearch[email_status]').remove();
                $('CustomerSearch[email2_status]').remove();
                $('CustomerSearch[mobile_app_status]').remove();

                //Creo la cadena de parametros de la url, con los valores seteados en los campos del filtro
                var params= 'CustomerSearch%5Bcompany_id%5D='+ $('#company_id').val() +
                    '&CustomerSearch%5Bzone_id%5D='+ $('#customersearch-zone_id').val() +
                    '&CustomerSearch%5Bcustomer_category_id%5D='+ $('#customer_category_id').val() +
                    '&CustomerSearch%5Bcustomer_class_id%5D='+ $('#customer_class_id').val() +
                    '&CustomerSearch%5Bnode_id%5D='+ $('#node_id').val() +
                    '&CustomerSearch%5Bplan_id%5D='+ $('#plan_id').val() +
                    customerStatus+
                    connectionStatus +
                    contractStatus +
                    emailStatus +
                    email2Status +
                    mobileAppStatus;

                //re direcciono a la misma pagina enviando los parametros ingresados
                location.href= '<?= yii\helpers\Url::to(['customers-by-node'])?>'+'&'+ params;
            });
        };


    };


</script>
<?php //$this->registerJs('CustomerSearch.init()')?>
