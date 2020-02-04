<?php

use app\modules\afip\models\TaxesBook;
use app\modules\employee\models\Employee;
use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\modules\afip\models\TaxesBook */

?>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <strong>
                <?=Yii::t('app', ($bills ? 'Employee Bills': 'Bills Added' ))?>
            </strong>
        </div>
        <div class="panel-body">
            <?php if ($model->status == TaxesBook::STATE_DRAFT && $bills) { ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php
                        echo Html::label(Yii::t('app', 'Employee'), ['provider_id']);
                        echo Select2::widget([
                            'data' => yii\helpers\ArrayHelper::map(Employee::find()->orderBy('lastname')->all(), 'employee_id', 'name' ),
                            'value' => $searchModel->employee_id,
                            'name' => 'provider_id',
                            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false, 'id'=>'employee_id'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ]
                        ]);
                    ?>
                </div>
                <div class="col-sm-6">
                    <?php
                    echo Html::label(Yii::t('app', 'Bill Types'), ['bill_types']);

                    echo Select2::widget([
                        'data' => yii\helpers\ArrayHelper::map(app\modules\sale\models\BillType::find()->where(['bill_type_id'=>$model->company->taxCondition->getBillTypesBuy()->select(['bill_type_id'])->asArray()->all()])->andWhere("BINARY `name` not like '%B%'")->all(), 'bill_type_id', 'name'),
                        'value' => $searchModel->bill_types,
                        'name' => 'bill_types[]',
                        'disabled' => true,
                        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false, 'id'=>'employee_bill_types'],
                        'pluginOptions' => [
                            'multiple'=>true
                        ],
                        'pluginEvents' => [
                            "change" => "function() { AddBuyBills.loadBills(); AddBuyBills.loadAddedBills(); }",
                        ]
                    ]);
                    ?>
                </div>
            </div>
            <?php } ?>
            <div class="row">
                <div class="col-sm-12">
                <?php
                $columns = [];
                if ($model->status == TaxesBook::STATE_DRAFT) {
                    $columns[] = [
                        'class' => 'yii\grid\CheckboxColumn',
                        'checkboxOptions' => function($model, $key, $index, $column) {
                            return ['value' => $model['employee_bill_id'], 'checked'=>($model['taxes_book_item_id']===null?0:1)];
                        },
                    ];
                }
                $columns = array_merge($columns, [
                        [
                            'header' => Yii::t('app','Employee'),
                            'value' => function($model){
                                return $model['fullName'];
                            },
                        ],
                        [
                            'label' => Yii::t('app', 'Bill'),
                            'value' => function($model){
                                return $model['bill_type'] . " - " . $model['number'];
                            }
                        ],
                        [
                            'label' => Yii::t('app', 'Date'),
                            'attribute' => 'date',
                            'format' => 'date'
                        ],
                        [
                            'label' => Yii::t('app', 'Amount'),
                            'attribute' => 'net',
                            'format' => 'currency',
                            'contentOptions'=>['class'=>'text-right']
                        ],
                        [
                            'label' => Yii::t('app', 'Taxes'),
                            'format' => 'currency',
                            'value' => function($model){
                               return $model['total'] - $model['net'];
                            },
                            'contentOptions'=>['class'=>'text-right']
                        ],
                        [
                            'label' => Yii::t('app', 'Total'),
                            'format' => 'currency',
                            'attribute' => 'total',
                            'contentOptions'=>['class'=>'text-right']
                        ]

                    ]);
                    echo GridView::widget([
                        'id' => 'w_employee_bills',
                        'dataProvider' => $dataProvider,
                        'columns' => $columns,
                    ]);
                ?>
                </div>
            </div>
        </div>
    </div>