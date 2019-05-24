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
use webvimark\modules\UserManagement\models\User;
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
                        echo Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->customer_id], [
                                'class' => 'btn btn-primary'
                        ]);
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
            
            <?php if(User::canRoute('/sale/customer/change-company')): ?>
            <?= Html::a('<span class="glyphicon glyphicon-chevron-right"></span> '.Yii::t('westnet', 'Change Company'), null, [
                        'class' => 'btn btn-warning',
                        'id'=>'change-company',
                    ])?>
            <?php endif; ?>
            
            
            <?= UserA::a('<span class="glyphicon glyphicon-chevron-right"></span> '.Yii::t('app', 'Discounts'), ['/sale/customer-has-discount/index',  'customer_id' => $model->customer_id], ['class' => 'btn btn-warning']) ?>
            <?= UserA::a('<span class="glyphicon glyphicon-chevron-right"></span> '.Yii::t('app', 'Products to Invoice'), ['/sale/product-to-invoice/index',  'customer_id' => $model->customer_id], ['class' => 'btn btn-warning']) ?>
            <?= UserA::a('<span class="glyphicon glyphicon-chevron-right"></span> '.Yii::t('app', 'Payment Plan'), ['/checkout/payment-plan/index',  'customer_id' => $model->customer_id], ['class' => 'btn btn-warning']) ?>
            
            <?php if(User::canRoute('/sale/bill/index')): ?>
            <div class="pull-right" style="margin-right: -10px;">
                <?= $this->render('_bills-dropdown', ['model' => $model, 'class' => 'btn btn-primary']); //Lista de comprobantes de cliente ?>
                <?= $this->render('_pending-bills', ['model' => $model]) ?>
            </div>
            <?php endif; ?>
            <?php if (User::canRoute('/sale/customer/send-message')):?>
            <div class="pull-right" style="margin-right: ; margin-top: 5px; ">
                <div class="dropdown">
                    <button class="btn btn-default" id="send-message" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo '<span class="glyphicon glyphicon-send"></span> '.Yii::t('app','Send...')?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="send-message">
                        <?php foreach ($messages as $message):?>
                            <li>
                                <?php echo \yii\bootstrap\Html::a($message->name, '#', ['class' => 'select_msj', 'data-message_id' => $message->customer_message_id])?>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
            <?php endif;?>
        </div>
    </div>

    <!--Contratos-->

    <?php if($contracts->getTotalCount() >= 1) {
        echo $this->render('_customer-contracts', [
            'model' => $model,
            'contracts' => $contracts
        ]);
    } else { ?>
        <label> <?= Yii::t('app', 'This customer doenst have any contract yet')?></label>
    <?php } ?>
    
    <!--Fin Contratos-->

    <h2> <?= Yii::t('app', 'Customer data')?>  </h2>

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
        [
            'attribute' => 'email',
            'value' => function ($model) {
                return Yii::$app->formatter->asEmail($model->email). ' ('. Yii::t('app',ucfirst($model->email_status)). ')';
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'email2',
            'value' => function ($model) {
                return Yii::$app->formatter->asEmail($model->email2). ' ('. Yii::t('app',ucfirst($model->email2_status)). ')';
            },
            'format' => 'raw'
        ],
        'phone',
        'phone2',
        'phone3',
        'phone4',
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
        'label'=> Yii::t('app', 'Payment methods and codes'),
        'value'=> function($model) {
            $payment_methods_and_codes = '';
            foreach ($model->getPaymentMethodNameAndCodes() as $payment_method_name) {
                $payment_methods_and_codes .= $payment_method_name['payment_method_name'] .': '. $payment_method_name['code'] .'.'.'<br>';
            }
            return $payment_methods_and_codes;
        },
        'format' => 'raw'
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

    $attributes[] = [
        'label' => Yii::t('app', 'Last update'),
        'value' => function ($model) {
            return $model->last_update;
        }
    ];

    
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
    ]) ?>    


    <?php //Está habilitado manejo de planes?
        if(Yii::$app->params['plan_product']):
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

<div class="modal fade" id="phoneModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo Yii::t('app','Select the phones to send message')?></h4>
            </div>
            <div class="modal-body">
                <?php if ($model->phone):?>
                    <?php echo Html::checkbox('phone', true, ['class' => 'phone-check','data-attr' => 'phone']) . $model->phone?>
                    <br>
                <?php endif;?>
                <?php if ($model->phone2):?>
                    <?php echo Html::checkbox('phone2', true, ['class' => 'phone-check', 'data-attr' => 'phone2']) . $model->phone2?>
                    <br>
                <?php endif;?>
                <?php if ($model->phone3):?>
                    <?php echo Html::checkbox('phone3', true, ['class' => 'phone-check', 'data-attr' => 'phone3']) . $model->phone3?>
                    <br>
                <?php endif;?>
                <?php if ($model->phone4):?>
                    <?php echo Html::checkbox('phone4', true, ['class' => 'phone-check', 'data-attr' => 'phone4']) . $model->phone4?>
                    <br>
                <?php endif;?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app','Close')?></button>
                <button type="button" class="btn btn-primary" id="send-message-btn"> <?php echo Yii::t('app','Send Message')?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    var CustomerView= new function(){
        
        this.init= function(){
            $(document).on('click', '#change-company', function(){
                $('#company-modal').modal();
            });
            $(document).on('click', '#btn-change-company', function(){
                CustomerView.changeCompany();
            });
            $(document).on('click', '.select_msj', function (e) {
                e.preventDefault();
                CustomerView.selectPhones($(this));
            })
            $(document).on('click', '#send-message-btn', function (e) {
                e.preventDefault();
                CustomerView.sendMessage($('#send-message-btn').data('message_id'))
            });
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
        
        this.selectPhones = function (opt) {
            $('#send-message-btn').data('message_id', $(opt).data('message_id'))
            $('#phoneModal').modal('show');
        }

        this.sendMessage = function (message) {
            var phones = [];


            var url = "<?php echo Url::to(['/sale/customer/send-message', 'customer_id' => $model->customer_id])?>&customer_message_id="+message;

            $.each($('.phone-check'), function (i, ch) {
                if ($(ch).is(':checked')) {
                    url = url + '&phones['+i+']='+$(ch).data('attr');
                }
            });

            location.replace(url);
        }

    }
</script>
<?php $this->registerJs('CustomerView.init();') ?>
    