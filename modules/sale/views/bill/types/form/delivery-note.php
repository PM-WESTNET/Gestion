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
                'options'=>['style'=>'width:25px;']
            ],
            [
                'class' => 'yii\grid\SerialColumn',
                'options'=>['style'=>'width:45px;']
            ],
            require(__DIR__ . '/_qty-column.php'),
            'concept',
            require(__DIR__ . '/_total-line-column.php'),
            /**[
                'attribute'=>'line_total',
                'format'=>'raw',
                'visible' => \app\modules\config\models\Config::getValue('show_price_delivery_note'),
                'value' => function($model){
                    return Html::tag('span', '$'.Html::tag('span', Yii::$app->formatter->asDecimal($model->line_total), [ 'data-update' => 'line_total' ]));
                }
            ]**/
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
    if(\app\modules\config\models\Config::getValue('show_price_delivery_note')):
    ?>
    <table class="table table-bordered">
        <tr style="font-weight: bold;">
            <td style="width: 50%;"></td>
            <td> 
                Total:
            </td>
            <td>
                <span>$</span>
                <span data-update-extra="billTotal">
                    <?php
                    $formatter = Yii::$app->formatter;
                    echo $formatter->asDecimal($model->calculateTotal(), 2);

                    ?>
                </span>
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
        <tr style="font-weight: bold;">
            <td colspan="2"><?= Yii::t('app', 'Order checked by:') ?></td>
        </tr>
        <tr>
            <td style="width: 50%; height: 1.6cm;">
                <?= Yii::t('app', 'Name') ?>
            </td>
            <td> 
                <?= Yii::t('app', 'Signature') ?>
            </td>
        </tr>
    </table>