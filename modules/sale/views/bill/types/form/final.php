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
            require(__DIR__ . '/_total-line-column.php'),
            /**[
                'attribute'=>'line_total',
                'format'=>'raw',
                'value' => function($model){
                    return Html::tag('span', '$'.Html::tag('span', Yii::$app->formatter->asDecimal($model->line_total), [ 'data-update' => 'line_total' ]));
                }
            ],**/
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
            <tr>
                <!-- <td style="width: 50%;"></td> -->
                <td  style="width: 50%; text-align: right;"> 
                    Total:
                </td>                
                <td data-title="Total"  style="width: 50%; font-weight: bold; text-align: center;">
                    <span data-update-extra="billTotal">
                        <?php
                        $formatter = Yii::$app->formatter;
                        echo $formatter->asCurrency($model->calculateTotal());
                        ?>
                    </span>
                </td>
            </tr>
       
       <!--      <tr style="font-weight: bold;">
            </tr> -->
                
        </table>
   </div>