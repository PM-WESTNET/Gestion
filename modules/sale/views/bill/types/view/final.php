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
                'attribute'=>'unit_final_price',
                'format'=>'currency'
            ],
            [
                'attribute'=>'line_total',
                'format'=>'currency'
            ],
            [
                'label'=>'Descuento',
                'format' => 'html',
                'value'=>function($model){
                    $labelType = '';
                    $bool = '';
                    $message = '';
                    if($model->discount){
                        $labelType = "success";
                        $bool = "Sí";
                        $message = ($model->discount->type == 'fixed')?"$".$model->discount->value:"%".$model->discount->value;
                    }else{
                        $labelType = "danger";
                        $bool = "No";
                    }
                    return "<div class='text-center'><span class='label label-$labelType'>$bool</span><br><span>$message</span></div>";
                }
            ],
            [
                'label'=>'Nombre Descuento',
                'format' => 'text',
                'value'=>function($model){
                    return ($model->discount)?$model->discount->name:null;
                }
            ],
        ],
        'options'=>[
            'style'=>'margin-top:10px;'
        ]
    ]);
    ?>
    
    <?php 
    /**
     * Tabla de totales:
     */
    ?>
   <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <td class="text-right">
                        <?php echo Yii::t('app', 'Discount') ?>
                    </td>
                    <td class="text-right"> 
                        Total:
                    </td>                
                </tr>
            </thead>
            <tbody>
                <tr style="font-weight: bold;">
                    <td class="text-right">
                        <?php
                        $formatter = Yii::$app->formatter;
                        echo $formatter->asCurrency($model->totalDiscountWithTaxes());
                        ?>
                    </td>
                    <td class="text-right">
                        <?php
                        $formatter = Yii::$app->formatter;
                        echo $formatter->asCurrency($model->total);
                        ?>
                    </td>
                </tr>
                
            </tbody>
        </table>
   </div>

