<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\employee\models\search\EmployeeSearch */
/* @var $dataEmployee yii\data\ActiveDataEmployee */

$this->title = Yii::t('app', 'Employee Account'). " $model->fullName";
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="employee-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=$this->render('_account-detail', ['employeeSearch' => $employeeSearch]);?>
    
    <div class="title">
        
        <p>
            <span>
                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                    'modelClass' => Yii::t('app','Employee Bill'),
                ]), ['employee-bill/create', 'employee'=>$model->employee_id, 'from'=>'account'], ['class' => 'btn btn-success']) ?>
            </span>
             <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                'modelClass' => Yii::t('app','Employee Payment'),
            ]), ['employee-payment/create', 'employee'=>$model->employee_id, 'from'=>'account'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>

    <h2>
        <?= Yii::t('app','Detail') ?>
    </h2>
    <div class="text-center">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['employee/current-account', 'id'=>$model->employee_id]
        ]); ?>
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="col-md-4">
                    <?php echo $form->field($searchModel, 'start_date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>
                </div>
                <div class="col-md-4">
                    <?php echo $form->field($searchModel, 'finish_date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>
                </div>
                <div class="col-md-4">
                    <?php echo $form->field($searchModel, 'type')->dropDownList(['all' => Yii::t('app', 'All'), 'payment'=>Yii::t('app', 'Payment'), 'bill'=> Yii::t('app', 'Bills')]) ?>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
        <div class="row">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'=>Yii::t('app','Company'),
                'value'=>function($model){
                    return $model['company_name'];
                }
            ],
            [
                'label'=>Yii::t('app','Type'),
                'value'=>function($model){
                    return Yii::t('app', $model['type']) . " - " . $model['number'];
                }
            ],
            [
                'label'=>Yii::t('app','Date'),
                'value'=>function($model){
                    return $model['date'];
                },
                'format' => 'date'
            ],
            [
                'label'=>Yii::t('app','Payment Method'),
                'value'=>function($model){
                    return $model['payment_method'];
                },
            ],
            [
                'label' => Yii::t('app', 'Debit'),
                'value'=>function($model) {
                    return Yii::$app->formatter->asCurrency( ($model['employee_bill_id']> 0) ?  $model['total'] : 0 );
                },
                'contentOptions'=>['class'=>'text-right'],
                'format' => 'raw'
            ],
            [
                'label' => Yii::t('app', 'Credit'),
                'value'=>function($model){
                    return Yii::$app->formatter->asCurrency( ($model['employee_payment_id']> 0) ?  $model['total'] : 0 );
                },
                'contentOptions'=>['class'=>'text-right'],
                'format' => 'raw'
            ],
            [
                'label' => Yii::t('app', 'Balance'),
                'value'=>function($model){
                    return Yii::$app->formatter->asCurrency( $model['saldo'] );
                },
                'contentOptions'=>['class'=>'text-right'],
                'format' => 'raw'
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>'{update} {view} {pdf} {open} {delete} {items}',
                'urlCreator' => function($action, $model, $key, $index) {
                    if($model['employee_bill_id']>0) {
                        $params['id'] = $model['employee_bill_id'];
                        $params[0] = '/employee/employee-bill/' . $action;
                    } else {
                        $params['id'] = $model['employee_payment_id'];
                        $params[0] = 'employee-payment/' . $action;
                    }
                    $params['return'] =  '/employee/employee-payment/current-account&employee_id='.$model['employee_id'];
                    return Url::toRoute($params);
                },
                'buttons'=>[
                    'update' => function ($url, $model, $key) {
                        return $model['status'] === 'draft' || $model['status'] === 'created' ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['class' => 'btn btn-primary']) : '';
                    },
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ['class' => 'btn btn-view']) ;
                    },
                    'delete' => function ($url, $model, $key) {
                        if($model['status'] === 'draft' || $model['status'] === 'created'  ){
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'class' => 'btn btn-danger',
                                'title' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'data-pjax' => '0',
                            ]);
                        }
                    },
                    'items' => function ($url, $model, $key) {
                        if(($model['employee_bill_id']> 0)) {
                            return '<a href="#" data-id="'.$model['employee_bill_id'].'" class="btn btn-warning btn-view-items"><span class="glyphicon glyphicon-list"></span></a>';
                        }
                        return "";
                    },
                ]
            ]
        ],
    ]); ?>
</div>
<!-- Modal -->
<div class="modal fade" id="modal-items" tabindex="-1" role="dialog" aria-labelledby="modal-items-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal-items-label"><?php echo Yii::t('app', 'Bill Items')  ?></h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close') ?></button>
            </div>
        </div>
    </div>
</div>
    <script>
        var EmployeeBill = new function(){
            this.init = function() {
                $(document).off('click', '.btn-view-items').on('click', '.btn-view-items', function(evt){
                    evt.preventDefault();
                    EmployeeBill.viewItems($(this).data('id'));
                });
            }

            this.viewItems = function(id){
                $.ajax({
                    url: '<?php echo Url::toRoute(['employee-bill/list-items']) ?>&employee_bill_id='+id,
                }).done(function(data){
                    $("#modal-items .modal-body").html(data);
                    $("#modal-items").modal('show');
                })
            }
        }
    </script>
<?php $this->registerJs('EmployeeBill.init()') ?>