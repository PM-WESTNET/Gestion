<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountMovement */
/* @var $form yii\widgets\ActiveForm */
    // Listado de Items
echo GridView::widget([
    'id'=>'items',
    'dataProvider' => $items,
    'showFooter' => true,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => Yii::t("accounting", "Account"),
            'value' => function($model){
                return ($model->account ? $model->account->name : '' );
            },
            'footer' => '<strong>'.Yii::t('app', 'Totals').'</strong>'
        ],
        [
            'label' => Yii::t("accounting", "Debit"),
            'value' => function($model){
                return ($model->debit ? $model->debit : 0 );
            },
            'format' => 'currency',
            'footer' => '<strong>'.Yii::$app->formatter->asCurrency( $model->getDebt() ) .'</strong>'
        ],
        [
            'label' => Yii::t("accounting", "Credit"),
            'value' => function($model){
                return ($model->credit ? $model->credit : 0 );
            },
            'format' => 'currency',
            'footer' => '<strong>' .Yii::$app->formatter->asCurrency( $model->getCredit() ) .'</strong>'
        ],
        [
            'class' => 'app\components\grid\ActionColumn',
            'template'=>'{update} {delete}',
            'buttons'=>[
                'delete'=>function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        null,
                        [
                            'data-url' => yii\helpers\Url::toRoute(['account-movement/delete-item', 'account_movement_id'=>$model->account_movement_id, 'account_movement_item_id'=>$model->account_movement_item_id]),
                            'title' => Yii::t('yii', 'Delete'),
                            'class' => 'deleteItem btn btn-danger'
                        ]);
                },
                'update'=>function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                        null,
                        [
                            'data-url' => yii\helpers\Url::toRoute(['account-movement/add-item', 'account_movement_id'=>$model->account_movement_id, 'account_movement_item_id'=>$model->account_movement_item_id]),
                            'title' => Yii::t('yii', 'Update'),
                            'class' => 'updateItem btn btn-primary'
                        ]);
                }
            ]
        ],
    ],
    'options'=>[
        'style'=>'margin-top:10px;'
    ]
]);
$diferencia = round($model->getDebt(), 2) - round($model->getCredit(),2);
?>
<div class="panel panel-success">
    <div class="panel-body collapse in" id="panel-body-items" aria-expanded="true">
        <div class="col-sm-2 col-md-offset-8">
            <?php echo Yii::t('accounting', 'Difference'); ?>
        </div>
        <div class="col-sm-2 <?php echo (round($diferencia,2) == 0 ? 'text-success' : 'text-danger' ) ?>">
            <strong><?php echo Yii::$app->formatter->asCurrency( $diferencia ) ?></strong>
        </div>
    </div>
</div>
