<?php

use app\modules\afip\models\TaxesBook;
use app\modules\provider\models\Provider;
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
                <?=Yii::t('app', ($bills ? 'Provider Bills': 'Bills Added' ))?>
            </strong>
        </div>
        <div class="panel-body">
            <?php if ($model->status == TaxesBook::STATE_DRAFT && $bills) { ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php
                        echo Html::label(Yii::t('app', 'Provider'), ['provider_id']);
                        echo Select2::widget([
                            'data' => yii\helpers\ArrayHelper::map(Provider::find()->orderBy('name')->all(), 'provider_id', 'name' ),
                            'value' => $searchModel->provider_id,
                            'name' => 'provider_id',
                            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false, 'id'=>'provider_id'],
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
                        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false, 'id'=>'bill_types'],
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
                            return ['value' => ($model['provider_bill_id']),
                                'checked'=>($model['taxes_book_item_id']===null?0:1),
                                'data-type' => ($model['type'] !== 'employee' ? 'provider' : 'employee')];
                        },
                    ];
                }
                $columns = array_merge($columns, [
                        [
                            'header' => Yii::t('app','Provider'),
                            'value' => function($model){
                                return $model['business_name'];
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
                        'id'    => ($bills ? 'w_bills' : 'w_bills_added' ),
                        'dataProvider' => $dataProvider,
                        'columns' => $columns,
                    ]);
                ?>
                </div>
            </div>
        </div>
    </div>