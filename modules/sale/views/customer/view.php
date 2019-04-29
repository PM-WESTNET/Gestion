<?php

use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use yii\db\Connection;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use app\components\helpers\UserA;
 /**
 * @var View $this
 * @var Customer $model
 */

$this->title = $model->code . ' - '.$model->fullName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
<div class="customer-view">


    <?php if(Yii::$app->params['class_customer_required']): ?>
    <?php 
        if(empty($model->customerClass)){
            $color = "black";
        }
        else if ($model->customerClass->colour === '#ffffff'){
            $color = "black";
        }
        else{
            $color = "white";
        }
        if(empty($model->customerClass->colour)){
            $bgColor = "white";
        }
        else{
            $bgColor = $model->customerClass->colour;
        }
    ?>
    <style>
           h1 {color: <?= $color ?>; padding: 20px;}
           h1 {background-color: <?= $bgColor ?>}
    </style>
    <?php endif; ?>
        
    <div class="title">
        <h1><?php echo Html::encode($this->title) ?></h1>
    </div>
    <div class="row">
        <div class="col-lg-12 noPadding">
            <p>
                <?php
                    if($model->canUpdate()){
                        echo Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->customer_id], ['class' => 'btn btn-primary']);
                    }
                ?>
                
                <?php if($model->deletable) echo UserA::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->customer_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>

                <?php
                //El modulo esta cargado?
                if(Yii::$app->getModule('checkout')): ?>
                <?= UserA::a('<span class="glyphicon glyphicon-usd"></span> '. Yii::t('app', 'Current account'), ['/checkout/payment/current-account', 'customer' => $model->customer_id], ['class' => 'btn btn-default']) ?>
                <?php endif; ?>

                <?php
                //Está habilitado manejo de planes?
                if(Yii::$app->params['plan_product']): ?>
                <?= UserA::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create Contract'), ['/sale/contract/contract/create',  'customer_id' => $model->customer_id], ['class' => 'btn btn-success pull-right']) ?>
                <?php endif; ?>
                <?= UserA::a(Yii::t('app', 'Customer Log'), ['/sale/customer-log/index', 'customer_id' => $model->customer_id], ['class' => 'btn btn-info']) ?>

                
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 noPadding">
            <?= UserA::a('<span class="glyphicon glyphicon-chevron-right"></span> '.Yii::t('app', 'Tickets'), ['customer/customer-tickets', 'id' => $model->customer_id], [
                        'class' => 'btn btn-warning',
                        'id'=>'customer-tickets',
                    ])?>
            <?= UserA::a('<span class="glyphicon glyphicon-chevron-right"></span> '.Yii::t('app', 'Carnet'), ['customer/customer-carnet', 'id' => $model->customer_id], [
                        'class' => 'btn btn-warning',
                        'id'=>'customer-tickets',
                    ])?>
            
            <?php if(\webvimark\modules\UserManagement\models\User::canRoute('/sale/customer/change-company')): ?>
            <?= Html::a('<span class="glyphicon glyphicon-chevron-right"></span> '.Yii::t('westnet', 'Change Company'), null, [
                        'class' => 'btn btn-warning',
                        'id'=>'change-company',
                    ])?>
            <?php endif; ?>
            
            
            <?= UserA::a('<span class="glyphicon glyphicon-chevron-right"></span> '.Yii::t('app', 'Discounts'), ['/sale/customer-has-discount/index',  'customer_id' => $model->customer_id], ['class' => 'btn btn-warning']) ?>
            <?= UserA::a('<span class="glyphicon glyphicon-chevron-right"></span> '.Yii::t('app', 'Products to Invoice'), ['/sale/product-to-invoice/index',  'customer_id' => $model->customer_id], ['class' => 'btn btn-warning']) ?>
            <?= UserA::a('<span class="glyphicon glyphicon-chevron-right"></span> '.Yii::t('app', 'Payment Plan'), ['/checkout/payment-plan/index',  'customer_id' => $model->customer_id], ['class' => 'btn btn-warning']) ?>
            
            <?php if(\webvimark\modules\UserManagement\models\User::canRoute('/sale/bill/index')): ?>
            <div class="pull-right" style="margin-right: -10px;">
                <?= $this->render('_bills-dropdown', ['model' => $model, 'class' => 'btn btn-primary']); //Lista de comprobantes de cliente ?>
                <?= $this->render('_pending-bills', ['model' => $model]) ?>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
    <?php
    
    $attributes = 
    [
        'code',
        'name',
        'lastname',
        [
            'attribute'=>'tax_condition_id',
            'value'=>$model->taxCondition ? $model->taxCondition->name : null,
        ],
        [
            'attribute'=>'document_type_id',
            'value'=>$model->documentType ? $model->documentType->name : null,
        ],
        'document_number',
        [
            'label'=>$model->getAttributeLabel('sex'),
            'value'=>Yii::t('app', ucfirst($model->sex))
        ],
        'email:email',
        'email2:email',
        'phone',
        'phone2',
        'phone3',
        //'address',
        [
            'label'=>$model->getAttributeLabel('status'),
            'value'=>Yii::t('app', ucfirst($model->status))
        ],
        [
            'label' => $model->getAttributeLabel('customer_reference_id'),
            'value' => ($model->customerReference ? ($model->customerReference->name . ( $model->customerReference->lastname!='' ? ', ' . $model->customerReference->lastname : '' )) : '' )
        ],
        [
            'label' => Yii::t('app','Publicity Shape'),
            'value' => Yii::t('app', $model->publicity_shape)
            ],
     ];
   
    $attributes[] = [
        'label'=>Yii::t('app', 'Address'),
        'format'=>'raw',
        'value'=> $model->address ? $model->address->shortAddress : '',
    ];
    $attributes[] = [
        'label'=>Yii::t('app', 'Address Indications'),
        'format'=>'raw',
        'value'=> $model->address ? $model->address->indications : '',
    ];

    if(Yii::$app->params['class_customer_required']){
        $attributes[]= 
                [
                'label'=>Yii::t('app', 'Customer Class'),
                'format'=>'raw',
                'value'=> $model->customerClass ? $model->customerClass->name : null,
                ];
    }
    
    if(Yii::$app->params['category_customer_required']){
        $attributes[]= 
                [
                'label'=>Yii::t('app', 'Customer Category'),
                'format'=>'raw',
                'value'=> $model->customerCategory ? $model->customerCategory->name : null,
                ];
    }
    
    $profileClasses = \app\modules\sale\models\Customer::getEnabledProfileClasses();
    
    foreach($profileClasses as $class){
        $attributes[] = $class->attr;
    }

    $parent = ($model->parent_company_id ? Company::findOne(['company_id' => $model->parent_company_id]) : null);
    $company = ($model->company_id ? Company::findOne(['company_id' => $model->company_id]) : null );


    $attributes[]=[
        'label'=> Yii::t('app', 'Company'),
        'value'=> ( $parent ? $parent->name : '' ) . ( ($parent ? '( ' : '' ) . ($company ? $company->name : '' ) . ($parent ? ') ' : '' ) )
    ];

    $attributes[]=[
        'label'=> Yii::t('app', 'Needs Bill'),
        'value'=> ( $model->needs_bill ? Yii::t('app', 'Yes') : 'No')
    ];

    $attributes[]=[
        'label'=> Yii::t('app', 'Payment Code'),
        'value'=> $model->payment_code
    ];
    
    $notifications_way= '';
    
    if(is_array($model->_notifications_way)){
        foreach ($model->_notifications_way as $way) {
            $notifications_way .= Yii::t('app', ucfirst($way)).', ';
        }
    }
    $attributes[] = [
        'label' => Yii::t('app', 'Notifications Way'),
        'value' => $notifications_way      
    ];
    
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
    ]) ?>    


    <?php //Está habilitado manejo de planes?
        if(Yii::$app->params['plan_product']):
    ?>
    <hr>
    <div class="title">
        <h2><?php echo Yii::t('app', 'Contracts')?></h2>
        <?php
        //Está habilitado manejo de planes?
        if(Yii::$app->params['plan_product']): ?>
        <p>
            <?= UserA::a(Yii::t('app', 'Create Contract'), ['/sale/contract/contract/create',  'customer_id' => $model->customer_id], ['class' => 'btn btn-success']) ?>
        </p>
        
        <?php endif; ?>
    </div>


    <?= GridView::widget([
                'dataProvider' =>\app\modules\sale\modules\contract\models\search\ContractSearch::getdataProviderContract($model->customer_id),
                //'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'contract_id',
                    'from_date',
                    [
                        'label'=> Yii::t('app', 'Status Account'),
                        'value'=>  function($model){ 
                            $con = app\modules\westnet\models\Connection::findOne(['contract_id' => $model->contract_id]);
                            return (!empty($con) ? Yii::t('app', ucfirst($con->status_account). ' Account'): null);
        
                        }
                    ],
                    [
                        'label'=> Yii::t('app', 'Address'),
                        'value'=> function($model){
                            return $model->address ? $model->address->shortAddress : '';
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Plan'),
                        'value' => function ( $model){
                            return $model->getPlan()->name;
                        }
                    ],     
                    ['class' => 'yii\grid\ActionColumn',
                        'template'=>'{view} {update}',
                        'buttons'=>[
                        'view' => function ($url, $model) {
                                if($model->canView()){
                                  return UserA::a('<span class="glyphicon glyphicon-eye-open"></span>',['/sale/contract/contract/view',  'id' => $model->contract_id], ['title' => Yii::t('yii', 'View'), 'class' => 'btn btn-view']);
                                }    
                            },
                        'update' => function ($url, $model) {
                                if ($model->canUpdate()){
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>',['/sale/contract/contract/update',  'id' => $model->contract_id], ['title' => Yii::t('yii', 'Update'), 'class' => 'btn btn-primary']);
                              
                                }
                           },
                        ],
                    ],
                ],
            ]);
     ?>

    <?php endif;?>
</div>

 <!-- Modal de company -->
    <div class="modal fade" id="company-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="top:25%">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo Yii::t('westnet', 'Change Company') ?></h4>
                </div>
                <div class="modal-body">
                    <?php
                    $form = ActiveForm::begin(['id'=>'form-company']);
                     
                        echo $form->field($model, 'company_id')
                            ->label(Yii::t('app', 'Company'))
                            ->dropDownList( ArrayHelper::map(Company::find()->andWhere(['not',['parent_id'=>null]])->all(), 'company_id', 'name' ), [
                                'prompt' => Yii::t('app','Select'), 'id'=> 'company_id'
                            ]);
                    ?>
                    <?php ActiveForm::end(); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel') ?></button>
                    <?php
                        echo UserA::a(Yii::t('app', 'Update'), null, [
                            'class' => 'btn btn-primary',
                            'id'=>'btn-change-company',
                            'data-loading-text' => Yii::t('app', 'Processing')."..."
                        ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
<script>
    var CustomerView= new function(){
        
        this.init= function(){
            $(document).on('click', '#change-company', function(){
                $('#company-modal').modal();
            });
            $(document).on('click', '#btn-change-company', function(){
                CustomerView.changeCompany();
            })
        }

        this.changeCompany= function(){
            $.ajax({
                url:'<?= Url::to(['/sale/customer/change-company', 'customer_id' => $model->customer_id])?>'+'&company_id='+$('#company_id').val(),
                method: 'POST',
                dataType: 'json',
                success: function(data){
                    if (data.status === 'success') {
                        window.location.reload();
                    }else{
                        alert('No se puede cambiar la empresa. Intente nuevamente.');
                    }
                }
            });
        }

    }
</script>
<?php $this->registerJs('CustomerView.init();') ?>
    