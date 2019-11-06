<?php

use app\modules\paycheck\models\Paycheck;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Dialog;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

if (!$embed) {
    $this->title = Yii::t('paycheck', 'Paychecks');
    $this->params['breadcrumbs'][] = $this->title;
}


?>
<style>
    .container{
        padding-left: 0px;
        padding-right: 0px;
    }
    #ui-datepicker-div {
        z-index: 9999 !important;
    }
</style>
<div class="paycheck-index" >
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <p>
            <?php
                if(!$embed){
                    echo Html::a('<span class="glyphicon glyphicon-plus"></span> ' .
                    Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('paycheck', 'Paycheck')]), ['create', 'embed'=>$embed], ['class' => 'btn btn-success']);
                }else{
                    echo "<a href='javascript:PaycheckIndex.create()' class='btn btn-success'><span class='glyphicon glyphicon-plus'></span>". Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('paycheck', 'Paycheck')]). '</a>';
                }
            ?>
        </p>
    </div>

    <?php

    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
         [
            'label' => 'Tipo',
            'value' => function($model){
                
                if ($model->is_own) {
                    return 'Propio';
                }elseif($model->to_order){
                    return 'A la orden';
                }else{
                    return 'Cruzado';
                }
            }
        ],
        /**
        [
            'label' => Yii::t('paycheck', 'Is Own'),
            'value' => function ($model) {
                return Yii::t('app', ($model->is_own ? "Yes" : "No" ));
            }
        ],
        [
            'label' => Yii::t('paycheck', 'To Order'),
            'value' => function ($model) {
                return Yii::t('app', ($model->to_order ? "Yes" : "No" ));
            }
        ],
        [
            'label' => Yii::t('paycheck', 'Crossed'),
            'value' => function ($model) {
                return Yii::t('app', ($model->crossed ? "Yes" : "No" ));
            }
        ],**/
        [
            'label' => Yii::t('app', 'Business Name'),
            'value' => 'business_name'
        ],
        [
            'label' => Yii::t('paycheck', 'Money Box'),
            'value' => function ($model) {
                if ($model->is_own) {
                    return /**$model->checkbook->moneyBoxAccount->moneyBox->name . " - ".**/ $model->checkbook->moneyBoxAccount->number;
                } else {
                    return $model->moneyBox->name;
                }
            }
        ],
        'number',
        'date:date',
        [
            'label' => 'Vencimiento', //Yii::t('paycheck', 'Due Date'),
            'value' => function ($model) {
                return Yii::$app->formatter->asDate($model->due_date);
            }
        ],
        'amount:currency',

    ];


    if (!$embed) {
        $columns[] = [
            'label' => Yii::t('app', 'Status'),
            'value' => function ($model) {
                return Yii::t('paycheck',$model->status);
            }
        ];


        $item = '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters');

        echo \yii\bootstrap\Collapse::widget([
            'items' => [
                [
                    'label' => $item,
                    'content' => $this->render('_search', ['model' => $searchModel, 'embed' => $embed]),
                    'encode' => false,
                ],
            ]
        ]);

        $columns[] = [
            'class' => 'app\components\grid\ActionColumn',
            'template'=>'{view} {update} {delete} {changeStatus}',
            'buttons'=>[
                'view' => function ($url, $model, $key) {
                    return '<a href="'.Url::toRoute(['paycheck/view', 'id'=>$model->paycheck_id]).'" class="btn btn-view"><span class="glyphicon glyphicon-eye-open"></span></a>';

                },
                'update' => function ($url, $model, $key) {
                    if($model->getUpdatable()) {
                        return '<a href="'.Url::toRoute(['paycheck/update', 'id'=>$model->paycheck_id]).'" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>';
                    }
                    return '';

                },
                'delete' => function ($url, $model, $key) {
                    return ($model->getDeletable() ? '<a href="'.Url::toRoute(['paycheck/delete', 'id'=>$model->paycheck_id]).
                        '" title="'.Yii::t('app','Delete').'" data-confirm="'.Yii::t('yii','Are you sure you want to delete this item?').'" data-method="post" data-pjax="0" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a>' : '');
                },
                'changeStatus' => function($url, $model, $key) { 
                    if(count($model->getPossibleStates())>=1) {
                        return ( '<a  class="btn btn-warning changeStatus" data-id="'.$model->paycheck_id.'" ><span class="glyphicon glyphicon-random"></span></a>');
                    }
                }
            ]
        ];
    } else {
        $columns[] = [
            'class' => 'app\components\grid\ActionColumn',
            'template' => '{select}',
            'buttons'=>[
                'select' => function ($url, $model, $key) {
                    return '<div class="btn btn-warning" onclick="if(typeof window.parent.window.SearchPaycheck != \'undefined\'){window.parent.window.SearchPaycheck.select(\''.$model->paycheck_id.'\')}">'.Yii::t('app','Select') . '</div>';
                }
            ]
        ];

    }
    //$columns =  array_merge($columns, $gridActions);

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $index, $widget, $grid){
            return ['class'=>(Yii::$app->formatter->asDate($model->due_date, 'yyyyMMdd') < date('Ymd', strtotime('+5 day ' . date('Ymd'))) ? "danger" : "" )];
        },
        'columns' => $columns,
        'options' => ['class' => 'table-responsive']          
    ]); ?>

</div>
<script>
    var PaycheckIndex = new function() {
        this.init = function(){
            $(document).off('click', '.changeStatus')
                .on('click', '.changeStatus', function(){
                PaycheckIndex.changeStatus($(this).data('id'));
            });

            $(document).off('click', '.saveState')
                .on('click', '.saveState', function(){
                    PaycheckIndex.save();
            });
            $(document).off('change', '#paycheck-status')
                .on('change', '#paycheck-status', function(){
                    PaycheckIndex.onChangeStatus($(this).val());
            });
            
        }

        this.changeStatus = function(id){
            $.ajax({
                url: '<?php echo Url::toRoute(['paycheck/change-state']) ?>&id=' + id,
                method: 'GET',
                dataType: 'html',
                success: function(data){
                    $("#modal-body").html(data);
                    $('#modalState').modal('show');
                }
            });
        }

        this.save = function(){
            $.ajax({
                url: $("#stateForm").attr('action'),
                data: $("#stateForm").serializeArray(),
                method: 'POST',
                dataType: 'json',
                success: function(data){
                    if(data.status == 'success') {
                        $('#modalState').modal('hide');
                        $("#modal-body").html('');
                        window.location.reload();
                    } else {
                        for(error in data.errors){
                            if(error != 'money_box_account_id'){
                                $('.field-paycheck-'+error).addClass('has-error');
                                $('.field-paycheck-'+error+' .help-block').text(data.errors[error]);
                            } else {
                                $('#bank-account-selector .help-block').addClass('has-error');
                                $('#bank-account-selector .help-block').text(data.errors[error]);
                            }
                        }
                    }
                }
            });
        }
        this.onChangeStatus = function(status) {
            if(status=='<?php echo Paycheck::STATE_DEPOSITED ?>') {
                $('#bank-account-selector').show();
            } else {
                $('#bank-account-selector').hide();
            }
        }
        
        this.create= function(){
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['paycheck/create'])?>' + '&embed=true&for_payment=true',
                method: 'POST',
                success: function(data){
                    $('.paycheck-index').parent().html(data);
                
                }       
                
            });
        }
    }
</script>
<?php  $this->registerJs("PaycheckIndex.init();"); ?>
<!-- Button trigger modal -->
<!-- Modal -->
<div class="modal fade" id="modalState" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo Yii::t('paycheck', 'Change State') ?></h4>
            </div>
            <div class="modal-body" id="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close') ?></button>
                <button type="button" class="btn btn-primary saveState"><?php echo Yii::t('app', 'Save') ?></button>
            </div>
        </div>
    </div>
</div>