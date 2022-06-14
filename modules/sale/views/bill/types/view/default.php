<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\bootstrap\Modal;

echo GridView::widget([
        'id'=>'grid',
        'dataProvider' => $detailsProvider,
        'options' => ['class' => 'table-responsive'],        
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
            ],
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'=>Yii::t('app','Quantity'),
                'options' => ['class' => 'input-sm'],        
                'attribute'=>'qty',
                'value' => function($model){
                    $value = $model->qty;
                    if($model->product && $model->product->unit->symbol_position == 'prefix'){
                        return $model->product->unit->symbol . " $value";
                    }
                    if($model->product && $model->product->unit->symbol_position == 'suffix'){
                        return "$value ". $model->product->unit->symbol;
                    }
                    return $value;
                }
            ],
            [
                'label'=>Yii::t('app','Secondary Quantity'),
                'attribute'=>'secondary_qty',
                'value' => function($model){
                    $value = $model->secondary_qty;
                    if($model->product && $model->product->secondary_unit_id && $model->product->secondaryUnit->symbol_position == 'prefix'){
                        return $model->product->secondaryUnit->symbol . " $value";
                    }
                    if($model->product && $model->product->secondary_unit_id && $model->product->secondaryUnit->symbol_position == 'suffix'){
                        return "$value ". $model->product->secondaryUnit->symbol;
                    }
                    return $value;
                },
                'visible'=>app\modules\config\models\Config::getValue('enable_secondary_stock'),
            ],
            'concept',
            [
                'attribute'=>'unit_net_price',
                'format'=>'currency'
            ],
            [
                'label' => Yii::t('app', 'Unit Net Discount'),
                'value' => function ($model){
                    return $model->unit_net_discount ? $model->unit_net_discount : 0 ;
                },
                'format'=>'currency'
            ],
            [
                'attribute'=>'line_subtotal',
                'format'=>'currency'
            ],
        ],
        'options'=>[
            'style'=>'margin-top:10px;'
        ]

        // 'options'=>[
        //     'class'=>'col-sm-12 table-bordered table-striped table-condensed'
        // ]
    ]);
    ?>
    


    <?php 
    /**
     * Tabla de totales:
     */
    $taxes = $model->getTaxesApplied();
    ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead style="font-weight: bold;">
                <tr>
                    <td style="width: 50%;"></td>
                    <td>
                        SUBTOTAL
                    </td>
                    <td>
                        <?php echo strtoupper( Yii::t('app', 'Total Discount') ) ?>
                    </td>
                    <td>
                        IVA
                    </td>
                    <td>
                        TOTAL
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr style="font-weight: normal;">
                    <td style="width: 50%;"></td>
                    <td data-title="SUBTOTAL">
                        <?php
                        $formatter = Yii::$app->formatter;
                        // echo $formatter->asCurrency($model->calculateAmount());
                        echo $formatter->asCurrency($taxes[1]['base']);
                        ?>
                    </td >
                    <td data-title="DISCOUNT">
                        <?php
                        $formatter = Yii::$app->formatter;
                        echo $formatter->asCurrency($model->totalDiscount());
                        ?>
                    </td >
                    <td data-title="IVA">
                        <?php
                        // echo $formatter->asCurrency($model->calculateTaxes()); 
                        echo $formatter->asCurrency($taxes[1]['amount']); 
                        ?>
                    </td>
                    <td data-title="TOTAL">
                        <?php
                        echo $formatter->asCurrency($model->calculateTotal()); 
                        ?>
                    </td>
                </tr>            
            </tbody>
        </table>
    </div>