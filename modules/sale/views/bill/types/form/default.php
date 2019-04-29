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
                'options'=>['style'=>'width:25px;']
            ],
            [
                'class' => 'yii\grid\SerialColumn',
                'options'=>['style'=>'width:45px;']
            ],
            require(__DIR__ . '/_qty-column.php'),
            'concept',
            require(__DIR__ . '/_unit-net-price-column.php'),
            [
                'attribute'=>'unit_net_price',
                'format'=>'raw',
                'visible' => !Yii::$app->params['bill_detail_price_updater'],
                'value' => function($model){
                    return Html::tag('span', '$'.Html::tag('span', Yii::$app->formatter->asDecimal($model->unit_net_price, 2), [ 'data-update' => 'unit_net_price' ]));
                }
            ],
            require(__DIR__ . '/_total-line-column.php'),        
            /**[
                'attribute'=>'line_subtotal',
                'format'=>'raw',
                'options'=>[
                    'style'=>'width:150px;',
                ],
                'value' => function($model){
                    return Html::tag('span', '$'.Html::tag('span', Yii::$app->formatter->asDecimal($model->line_subtotal, 2), [ 'data-update' => 'line_subtotal' ]));
                }
            ],**/
            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>'{delete-detail}',
                'buttons'=>[
                    'delete-detail'=>function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'class' => 'btn btn-danger',
                        ]);
                    }
                ]
            ],
        ],
        'options'=>[
            'style'=>'margin-top:0px;',
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
            <thead style="font-weight: bold;">
                <tr>
                    <td style="width: 50%;"></td>
                    <td> 
                        SUBTOTAL
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
                        <span data-update-extra="billAmount"><?php echo Yii::$app->formatter->asCurrency($model->calculateAmount()); ?></span>
                    </td>
                    <td data-title="IVA">
                        <span data-update-extra="billTaxes"><?php echo Yii::$app->formatter->asCurrency($model->calculateTaxes()); ?></span>
                    </td>
                    <td data-title="TOTAL">
                        <span data-update-extra="billTotal"><?php echo Yii::$app->formatter->asCurrency($model->calculateTotal()); ?></span>
                    </td>
                </tr>            
            </tbody>
        </table>
    </div>