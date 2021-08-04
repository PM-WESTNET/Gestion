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
use app\modules\firstdata\models\FirstdataAutomaticDebit;
use app\modules\automaticdebit\models\AutomaticDebit;
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

    <?php
        if ($model->hasPendingPlanChange()):
    ?>
        <?php $change = $model->getPendingPlanChange()?>
        <div class="alert alert-warning">
            <h4><?php echo Yii::t('app','The customer has pending programmed plan change for {date}', ['date' => $change->date])?></h4>
        </div>
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
                    <?php if(User::canRoute('/sale/contract/contract/create')): ?>
                        <?= UserA::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create Contract'), ['/sale/contract/contract/create',  'customer_id' => $model->customer_id], ['class' => 'btn btn-success pull-right']) ?> <br>
                    <?php endif; ?>
                    <?= UserA::a('<span class="glyphicon glyphicon-time"></span> '.Yii::t('app', 'Create programmed plan change'), ['/sale/contract/programmed-plan-change/create',  'customer_id' => $model->customer_id], ['class' => 'btn btn-warning pull-right']) ?>
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
            <?= UserA::a('<span class="glyphicon glyphicon-chevron-right"></span> '.Yii::t('app', 'Ticket Managements'), ['/ticket/ticket-management/customer-index',  'customer_id' => $model->customer_id], ['class' => 'btn btn-warning']) ?>

            <?php if(User::canRoute('/sale/bill/index')): ?>
            <div class="pull-right" style="margin-right: -10px;">

                <?= $this->render('_bills-dropdown', ['model' => $model, 'class' => 'btn btn-primary']); //Lista de comprobantes de cliente ?>
                <?= $this->render('_pending-bills', ['model' => $model]) ?>
            </div>
            <?php endif; ?>

            <?php if (User::canRoute('/sale/customer/send-message')):?>
            <div class="pull-right" style="margin-right: ; margin-top: 5px; ">
                <div class="dropdown">
                    <button class="btn btn-default" id="send-message" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                     <?= $model->canSendSMSMessage() ? '': 'disabled'?>>
                        <?= '<span class="glyphicon glyphicon-send"></span> '.Yii::t('app','Send...')?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="send-message">
                        <?php foreach ($messages as $message):?>
                            <li>
                                <?= \yii\bootstrap\Html::a($message->name, '#', ['class' => 'select_msj', 'data-message_id' => $message->customer_message_id])?>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Botón de Pago <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><?= \yii\bootstrap\Html::a('Whatsapp <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                          <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                        </svg>', '#', ['class' => 'btn btn-success select_msj_payment_button'])?></li>
                    <li><?= \yii\bootstrap\Html::a('Email  <i class="glyphicon glyphicon-send"></i>', '#', ['class' => 'btn btn-danger select_email_payment_button'])?></li>
                </ul>
            </div>
            <?php endif;?>
        </div>
    </div>

    <!--Contratos-->
    <hr>
    <?php if($contracts->getTotalCount() >= 1) {
        echo $this->render('_customer-contracts', [
            'model' => $model,
            'contracts' => $contracts,
            'products' => $products,
            'vendors' => $vendors
        ]);
    } else { ?>
        <label> <?= Yii::t('app', 'This customer doenst have any contract yet')?></label>
    <?php } ?>
    <hr>
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
        'birthdate',
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
            'attribute' => 'has_debit_automatic',
            'value' => function($model) {
                if ($model->has_debit_automatic === 'yes') {
                    if (FirstdataAutomaticDebit::find()->andWhere(['customer_id' => $model->customer_id, 'status' => 'enabled'])->exists()) {
                        return Yii::t('app', 'Yes') . ' ('. Yii::t('app', 'Active') . ')';
                    }

                    return Yii::t('app', 'Yes') . ' ('. Yii::t('app', 'Pending') . ')';
                }

                return 'No';
            },
            'label' => Yii::t('app','Require Automatic Debit')
        ],
        [
            'attribute' => 'has_direct_debit',
            'value' => function($model) {
                if ($model->has_direct_debit) {
                    if (AutomaticDebit::find()->andWhere(['customer_id' => $model->customer_id, 'status' => 10])->exists()) {
                        return Yii::t('app', 'Yes') . ' ('. Yii::t('app', 'Active') . ')';
                    }

                    return Yii::t('app', 'Yes') . ' ('. Yii::t('app', 'Pending') . ')';
                }

                return 'No';
            },
            'label' => Yii::t('app','Require Direct Debit')
        ],
        [
            'label'=>$model->getAttributeLabel('status'),
            'value'=>Yii::t('app', ucfirst($model->status))
        ],
        [
            'label' => $model->getAttributeLabel('customer_reference_id'),
            'value' => function ($model) {
                return ($model->customerReference ? Html::a($model->customerReference->fullName, ['customer/view', 'id' => $model->customer_reference_id]) : '');
            },
            'format' => 'raw'
        ],
        [
            'label' => Yii::t('app','Publicity Shape'),
            'value' => Yii::t('app', $model->publicity_shape)
        ],
     ];

    $attributes[] = [
        'attribute' => 'observations',
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
    
    $profileClasses = Customer::getEnabledProfileClasses();
    
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

    $attributes[] = [
        'label' => Yii::t('app', 'Last update'),
        'value' => function ($model) {
            return $model->last_update;
        }
    ];

    $attributes[] = [
        'label' => Yii::t('app', 'Has mobile app installed').' '.'<span class="glyphicon glyphicon-phone"></span>' ,
        'value' => function ($model) {
            return $model->hasMobileAppInstalled() ? Yii::t('app', 'Yes') : Yii::t('app', 'No');
        },
    ];

    if($model->hasMobileAppInstalled()) {
        $attributes[] = [
            'label' => Yii::t('app', 'Last app use').' '.'<span class="glyphicon glyphicon-phone"></span>' ,
            'value' => function ($model) {
                $last_use = $model->lastMobileAppUse(true);
                return $last_use ? $last_use : '';
            },
        ];
    }

    $attributes[] = [
        'label' => Yii::t('app', 'Document image') ,
        'value' => function ($model) {
            $src = $model->getDocumentImageWebPath();
            $src_arr = explode('.', $src);
            $ext = $src_arr[count($src_arr) - 1];

            if ($ext === 'jpg' || $ext === 'jpeg' || $ext === 'png') {
                return Html::img($model->getDocumentImageWebPath(), ['class' => 'img-responsive']);
            } else {
                return Html::img('images/pdf-icon.jpg', ['width' => "40px", 'height' => "40px"]) . ' '. Html::a(Yii::t('app', 'Download File'), $src, ['target' => "_blank"]);
            }

        },
        'format' => 'raw'
    ];

    $attributes[] = [
        'label' => Yii::t('app', 'Tax image') ,
        'value' => function ($model) {
            $src = $model->getTaxImageWebPath();
            $src_arr = explode('.', $src);
            $ext = $src_arr[count($src_arr) - 1];

            if ($ext === 'jpg' || $ext === 'jpeg' || $ext === 'png') {
                return Html::img($model->getTaxImageWebPath(), ['class' => 'img-responsive']);
            } else {
                return Html::img('images/pdf-icon.jpg', ['width' => "40px", 'height' => "40px"]) . ' '. Html::a(Yii::t('app', 'Download File'), $src, ['target' => "_blank"]);
            }
        },
        'format' => 'raw'
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


<div class="modal fade" id="phoneModalPaymentButton" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo Yii::t('app','Select the phones to send message')?></h4>
            </div>
            <div class="modal-body">
                <?php 
                    if($model != null && $model->hash_customer_id == null){
                        $model->hash_customer_id = md5($model->customer_id);
                        $model->save(false);
                    }
                    $url_whatsapp = str_replace('${customer_id}',$model->hash_customer_id,$url_whatsapp);
                ?>
                <?php if ($model->phone):?>
                    <?php $url_whatsapp_phone = str_replace('${phone}',$model->phone,$url_whatsapp);?>
                    <?= \yii\bootstrap\Html::a('Teléfono 1 ('.$model->phone.')', $url_whatsapp_phone,['target' => '_blank'])?>
                    <br>
                <?php endif;?>
                <?php if ($model->phone2):?>
                    <?php $url_whatsapp_phone2 = str_replace('${phone}',$model->phone2,$url_whatsapp);?>
                    <?= \yii\bootstrap\Html::a('Teléfono 2 ('.$model->phone2.')', $url_whatsapp_phone2,['target' => '_blank'])?>
                    <br>
                <?php endif;?>
                <?php if ($model->phone3):?>
                    <?php $url_whatsapp_phone3 = str_replace('${phone}',$model->phone3,$url_whatsapp);?>
                    <?= \yii\bootstrap\Html::a('Teléfono 3 ('.$model->phone3.')', $url_whatsapp_phone3,['target' => '_blank'])?>
                    <br>
                <?php endif;?>
                <?php if ($model->phone4):?>
                    <?php $url_whatsapp_phone4 = str_replace('${phone}',$model->phone4,$url_whatsapp);?>
                    <?= \yii\bootstrap\Html::a('Teléfono 4 ('.$model->phone4.')', $url_whatsapp_phone4,['target' => '_blank'])?>
                    <br>
                <?php endif;?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app','Close')?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="emailModalPaymentButton" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo Yii::t('app','Select the emails to send message')?></h4>
            </div>
            <div class="modal-body">
                <?php if ($model->email_status == "active"):?>
                    <?= \yii\bootstrap\Html::a('Email ('.$model->email.')', ['send-payment-button-email','email' => $model->email, 'customer_id' => $model->customer_id],['target' => '_blank'])?>
                    <br>
                <?php endif;?>
                <?php if ($model->email2_status == "active"):?>
                    <?= \yii\bootstrap\Html::a('Email 2 ('.$model->email2.')', ['send-payment-button-email','email' => $model->email2, 'customer_id' => $model->customer_id],['target' => '_blank'])?>
                    <br>
                <?php endif;?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app','Close')?></button>
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
            $(document).on('click', '.select_msj_payment_button', function (e) {
                e.preventDefault();
                CustomerView.selectPhonesPaymentButton($(this));
            })
            $(document).on('click', '.select_email_payment_button', function (e) {
                e.preventDefault();
                CustomerView.selectEmailsPaymentButton($(this));
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

        this.selectPhonesPaymentButton = function (opt) {
            $('#phoneModalPaymentButton').modal('show');
        }

        this.selectEmailsPaymentButton = function (opt) {
            $('#emailModalPaymentButton').modal('show');
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
    