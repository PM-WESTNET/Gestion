<?php

use app\modules\sale\models\search\CustomerSearch;
use yii\bootstrap\Collapse;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;
use webvimark\modules\UserManagement\models\User;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var CustomerSearch $searchModel
 */

$this->title = Yii::t('app', 'Cashing panel');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>        
    </div>
    
    <div class="debtors-search">

    <?php
    $item = '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters');

    echo Collapse::widget([
        'items' => [
            [
                'label' => $item,
                'content' => $this->render('_filters-debtors', ['searchModel' => $searchModel, 'action' => 'cashing-panel']),
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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'grid',
        'rowOptions' => function($model){
                        return ['data' => [ 'key' => $model['code']]];
                    },
        'columns' => [
            ['class' => '\yii\grid\CheckboxColumn'],
            [
                'label' => Yii::t('app', 'Customer Number'),
                'value' => 'code',
            ],
            [
                'label' => Yii::t('app', 'Customer'),
                'attribute'=>'name',
            ],
            [
                'attribute'=>'saldo',
                'format'=>'currency',
                'label' => Yii::t('app', 'Amount due'),
            ],
            [
                'attribute'=> 'debt_bills',
                'label' => Yii::t('app', 'Debt Bills'),
            ],
            [
                'class' => '\yii\grid\DataColumn',
                'content' => function($model, $key, $index, $column){
                    return Html::a('<span class="glyphicon glyphicon-usd"></span> '.Yii::t('app','Account'), ['/checkout/payment/current-account','customer' => $model['customer_id']], ['class'=>'btn btn-width btn-default']);
                },
                'format'=>'html',
            ]
        ],
    ]); ?>

    <?= Html::a(Yii::t('app', 'Create and assing tickets'), ['#'], [
        'class' => 'btn btn-default pull-right',
        'id' => 'create-tickets-btn',
    ])?>

</div>

<?php Modal::begin([
    'header' => '<h2>'.Yii::t('app', 'Masive assignation').'<img src="images/ajax-loader.gif" id="load_gif" style="display: none"></h2>',
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
<input class="text hidden" name="category_id" id="massive-assign-category_id" value="<?php echo \app\modules\config\models\Config::getValue('cobranza_category_id')?>">


        <label style="padding-top: 20px"> <?= Yii::t('app', 'Assign to')?> : </label>
        <?= Select2::widget([
            'id' => 'user_id',
            'data' => ArrayHelper::map(User::find()->where(['status' => 1])->all(), 'id', 'username'),
            'name' => 'user_id',
            'options' => [
                'placeholder' => Yii::t('app','Select an user ...'),
                'allowClear' => true,
            ],
        ]);

    ActiveForm::end();
Modal::end();?>

<script>
    var CashingPanel = new function() {

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
            })

            $('#masive-assign').on('click', function (evt) {
                evt.preventDefault();
                if($('#massive-assign-title').val() == '' || $('#user_id').val() == ''){
                    alert('Complete todos los campos para continuar');
                } else {
                    CashingPanel.createTicketsAndAssignUser();
                }
            });
            this.markCustomersWithCobranzaTicket();
        }

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
                data: {customer_codes: codes, category_id: "<?php echo \app\modules\config\models\Config::getValue('cobranza_category_id')?>"},
                dataType: 'json',

            }).done(function(data, status){
                $('#masive-assign').removeAttr('disabled');
                if (status === 'success') {

                    $.each(data, function( index, value ) {
                        if(value.has_ticket) {
                            $('[data-key="'+value.customer_code+'"]').find('input').prop('disabled', 'disabled');
                        }
                    });
                }else{
                    console.log(data);
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
                        CashingPanel.markCustomersWithCobranzaTicket();
                    }
                }
            });
        }
    }

</script>
<?php $this->registerJs('CashingPanel.init()')?>
