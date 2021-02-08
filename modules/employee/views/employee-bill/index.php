<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use yii\bootstrap\Collapse;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\employee\models\search\EmployeeBillSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Employee Bills') . ( $employee !== null ?  " - " . $employee->name : "" ) ;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-bill-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                    'modelClass' => Yii::t('app','Employee Bill'),
                ]), ['employee-bill/create', 'employee'=>($employee ? $employee->employee_id : null )], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    <?= Collapse::widget([
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters'),
                    'content' => $this->render('_employee-bill-filters', ['model' => $searchModel]),
                    'encode' => false,
                ],
            ],
            'options' => [
                'class' => 'hidden-print'
            ]
        ]);
    ?>
    <?php
    $columns[] = ['class' => 'yii\grid\SerialColumn'];
    if ($employee===null) {
        $columns[] = [
            'header' => Yii::t('app','Employee'),
            'attribute' => 'employee_id',
            'value' => function($model){ return $model->employee->name; },
        ];
    }
    $columns[] = [
        'label' => Yii::t('app', 'Bill Type'),
        'attribute' => 'billType.name'
    ];
    $columns[] = 'number';
    $columns[] = [ 'attribute' => 'date', 'format' => 'date' ];
    $columns[] = 'net:currency';
    $columns[] = 'taxes:currency';
    $columns[] = 'total:currency';
    $columns[] = [
        'label' => Yii::t('app', 'Status'),
        'value' => function ($model) {
            return Yii::t('app', ucfirst($model->status));
        }
    ];

    $export_columns = $columns;

    $columns[] = [
        'class' => 'app\components\grid\ActionColumn',
        'template'=>'{view} {update} {delete} {items}',
        'buttons' => [
            'update' => function ($url, $model, $key) {
                if ($model->getUpdatable()) {
                    return '<a href="' . Url::toRoute(['employee-bill/update', 'id' => $model->employee_bill_id]) . '" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>';
                }
            },
            'view' => function ($url, $model, $key) {
                return '<a href="'.Url::toRoute(['employee-bill/view', 'id'=>$model->employee_bill_id]).'" class="btn btn-view"><span class="glyphicon glyphicon-eye-open"></span></a>';
            },
            'delete' => function ($url, $model, $key) {
                return (!$model->deletable ? "" : '<a href="'.Url::toRoute(['employee-bill/delete', 'id'=>$model->employee_bill_id]).
                '" title="'.Yii::t('app','Delete').'" data-confirm="'.Yii::t('yii','Are you sure you want to delete this item?').'" data-method="post" data-pjax="0" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a>');
            },
            'items' => function ($url, $model, $key) {
                return '<a href="#" data-id="'.$model->employee_bill_id.'"class="btn btn-warning btn-view-items"><span class="glyphicon glyphicon-list"></span></a>';
            },
        ]
    ];
    ?>

    <!-- Modulo Export -->
    <?= ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'columns' => $export_columns,
        'showConfirmAlert'=>false
    ]);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'columns' => $columns
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