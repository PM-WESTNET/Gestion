<?php

use app\modules\sale\models\Address;
use app\modules\sale\modules\contract\models\search\ContractSearch;
use kartik\export\ExportMenu;
use kartik\select2\Select2;
use webvimark\modules\UserManagement\models\User;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Collapse;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;


/**
 * @var View $this
 * @var ContractSearch $contract_search
 */

$this->title = Yii::t('app', 'Installations');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="installations">
    <h1> <?= $this->title?></h1>
    
    <div class="installations-search">

    <?php
        $item = '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters');

        echo Collapse::widget([
            'items' => [
                [
                    'label' => $item,
                    'content' => $this->render('_filters-installations', ['model' => $contract_search]),
                    'encode' => false,
                ],
            ],
            'options' => [
                'class' => 'print',
                'aria-expanded' => 'false'
            ]
        ]);
    ?>
    </div>

    <?=
    ExportMenu::widget([
        'dataProvider' => $data,
        'columns' => [
            [
                'label'=> Yii::t('app', 'Customer Number'),
                'value'=>function($model){
                    return  $model['code'];
                },
                'contentOptions' => ['style' => 'text-align: left'],
            ],
            [
                'label'=> Yii::t('app', 'Customer'),
                'value'=>function($model){
                    return  $model['name'];
                },
                'contentOptions' => ['style' => 'text-align: left'],
            ],
            [
                'label'=> Yii::t('app', 'Phones'),
                'value'=>function($model){
                    return  $model['phones'];
                }
            ],
            [
                'label'=> Yii::t('app', 'Email'),
                'value'=>function($model){
                    return  $model['email'];
                }
            ]
        ],
    ]);

    echo GridView::widget([
        'dataProvider' => $data,
        'id' => 'grid',
        'rowOptions' => function($model){
            return ['data' => [ 'key' => $model['code']]];
        },
        'columns' => [
            ['class' => '\yii\grid\CheckboxColumn'],
            [
                'label' => Yii::t('app', 'Customer Number'),
                'attribute' => 'code'
            ],
            [
                'label' => Yii::t('app', 'Customer'),
                'attribute' => 'name'
            ],
            [
                'label' => Yii::t('westnet', 'Connection'),
                'value' => function($model){
                    $a = Address::findOne(['address_id' => $model['address_id']]);
                    if($a){
                        return $a->fullAddress;
                    }
                    return "";
                }
            ],
            [
                'label' => Yii::t('app', 'Phones'),
                'attribute' => 'phones'
            ],
            [
                'label' => Yii::t('app', 'From Date'),
                'value' => function($model){
                    return Yii::$app->formatter->asDate($model['from_date'], 'dd-MM-yyyy');
                },
            ],        
            [
                'label' => Yii::t('app', 'Bills Count'),
                'attribute' => 'bills',
            ],
            
            [
                'label' => Yii::t('app', 'Balance'),
                'value' => function ($model){
                    return Yii::$app->formatter->asCurrency($model['saldo']);
                }
            ],
            [
                'label' => Yii::t('app', 'Tickets Count'),
                'attribute' => 'ticket_count',
            ], 
            [
                'label' => Yii::t('app', 'Account'),
                'content' => function($model, $key, $index, $column) {
                        return Html::a('<span class="glyphicon glyphicon-usd"></span> ' . Yii::t('app', 'Account'), ['/checkout/payment/current-account', 'customer' => $model['customer_id']], ['class' => 'btn btn-sm btn-default']);
                    },
            ],        
            [
                'class' => 'app\components\grid\ActionColumn',
                'buttons' => [
                    'view' => function($url, $model, $key){
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                Url::to(['/sale/contract/contract/view', 
                                'id'=> $model['contract_id']]), ['class' => 'btn btn-view']);
                    },
                    'ticket' => function($url, $model, $key){
                        return Html::a('<span class= "glyphicon glyphicon-tags"></span>', 
                                Url::to(['/sale/customer/customer-tickets', 
                                'id'=> $model['customer_id']]), ['class' => 'btn btn-warning']);
                    },
                ],
                'template' => '{view} {ticket}  '
                          
            ]
        ]
    ])
        
    ?>

    <?= Html::a(Yii::t('app', 'Create and assing tickets'), ['#'], [
        'class' => 'btn btn-default pull-right',
        'id' => 'create-tickets-btn',
    ])?>
    
</div>

<?php Modal::begin([
    'header' => '<h2>'.Yii::t('app', 'Masive assignation').'</h2>',
    'options' => [
        'id' => 'assign-modal',
        'tabindex' => null
    ],
    'footer' =>  Html::a(Yii::t('app', 'Acept'), ['#'], [
        'class' => 'btn bt-default pull-right',
        'id' => 'masive-assign',
    ])
]);

    ActiveForm::begin(['id' => 'massive-assign-form'])?>
        <label> <?= Yii::t('app', 'Title')?> </label>
        <input class="text form-control" name="title" id="massive-assign-title">

        <input class="text hidden" name="customer_codes" id="massive-assign-customer-codes">
        <input class="text hidden" name="category_id" id="massive-assign-category_id" value="<?php echo \app\modules\config\models\Config::getValue('installations_category_id')?>">

        <label style="padding-top: 20px"> <?= Yii::t('app', 'Assign to')?> : </label>
        <?= Select2::widget([
            'id' => 'user_id',
            'data' => $users,
            'name' => 'user_id',
            'options' => [
                'placeholder' => Yii::t('app','Select an user ...'),
                'allowClear' => true,
            ],
        ]);

    ActiveForm::end();
Modal::end();?>

<script>
    var Installations = new function() {

        this.init = function () {
            $('#create-tickets-btn').on('click', function (evt) {
                evt.preventDefault();
                var selection = $('#grid').yiiGridView('getSelectedRows');
                if(selection.length  == 0) {
                    alert('Debe seleccionar al menos un elemento');
                } else {
                    $('#massive-assign-customer-codes').val(selection);
                    $('#assign-modal').modal();
                }
            });

            $('#masive-assign').on('click', function (evt) {
                evt.preventDefault();
                if($('#massive-assign-title').val() == '' || $('#user_id').val() == ''){
                    alert('Complete todos los campos para continuar');
                } else {
                    Installations.createTicketsAndAssignUser();
                }
            });
            this.markCustomersWithCobranzaTicket();
        };

        /**
         * Busca todos los clientes de la página renderizada y verifica que no tengan un ticket creado de la categoria cobranza,
         * de tenerlo, desabilita el checkbox
         */
        this.markCustomersWithCobranzaTicket = function () {
            var elements = $('#grid').find('table').find('tbody').find('tr');
            var codes = [];
            elements.each(function(){
                codes.push($(this).data('key'));
            });

            $.ajax({
                url: '<?= Url::to(['/ticket/ticket/customers-has-category-ticket'])?>',
                method: 'POST',
                data: {customer_codes: codes, category_id: "<?php echo \app\modules\config\models\Config::getValue('installations_category_id')?>"},
                dataType: 'json',
                success: function(data){
                    $.each(data, function( index, value ) {
                        if(value.has_ticket) {
                            $('[data-key="'+value.customer_code+'"]').find('input').prop('disabled', 'disabled');
                        }
                    });
                }
            });
        }

        /**
         * Crea tickets para los clientes seleccionados y los asigna al usuario indicado.
         */
        this.createTicketsAndAssignUser = function () {
            $.ajax({
                url: '<?= Url::to(['/ticket/ticket/create-and-assign-user'])?>',
                method: 'POST',
                data: $('#massive-assign-form').serializeArray(),
                dataType: 'json',
                beforeSend: function () {
                    $('#masive-assign').attr('disabled', 'disabled');
                    $('#load_gif').show();
                },
                success: function(data){
                    $('#masive-assign').removeAttr('disabled');
                    $('#load_gif').hide();
                    if(data.status == 'success') {
                        $('#assign-modal').modal('hide');
                        Installations.markCustomersWithCobranzaTicket();
                    }
                }
            });
        }
    }

</script>
<?php $this->registerJs('Installations.init()')?>
