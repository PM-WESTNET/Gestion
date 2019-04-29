<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\bootstrap\Modal;

echo GridView::widget([
        'id'=>'grid',
        'dataProvider' => $detailsProvider,
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
                'attribute'=>'line_total',
                'format'=>'currency',
                'visible' => \app\modules\config\models\Config::getValue('show_price_delivery_note')
            ],
            [
                'label' => Yii::t('app', 'Verification'),
                'visible' => \app\modules\config\models\Config::getValue('show_delivery_note_verification_column'),
                'value' => function(){ return ''; }
            ]
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
    if(\app\modules\config\models\Config::getValue('show_price_delivery_note')):
    ?>
    <table class="table table-bordered">
        <tr style="font-weight: bold;">
            <td style="width: 50%;"></td>
            <td> 
                Total:
            </td>
            <td>
                <?php
                $formatter = Yii::$app->formatter;
                echo $formatter->asCurrency($model->calculateAmount()); 
                ?>
            </td>
        </tr>
    </table>
    <?php endif; ?>

    <?php 
    /**
     * Tabla de conformidad:
     */
    ?>
    <table class="table table-bordered">
        <tr>
            <td colspan="2"><?= Yii::t('app', 'Order checked by:') ?></td>
        </tr>
        <tr style="font-weight: bold;">
            <td style="width: 50%; height: 1.6cm;">
                <?= Yii::t('app', 'Name') ?>
            </td>
            <td> 
                <?= Yii::t('app', 'Signature') ?>
            </td>
        </tr>
    </table>