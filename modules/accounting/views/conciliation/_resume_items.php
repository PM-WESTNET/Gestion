<?php

use app\modules\accounting\models\ConciliationItem;
use app\modules\accounting\models\Resume;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\Dialog;
use yii\widgets\Pjax;

?>

<?php Pjax::begin(['id' => 'r_items'])?>
<div class="row">
    <div class="col-sm-6 text-center">
        <strong><?= Yii::t('accounting', 'Initial Balance'); ?></strong>
        <br/>
        <?= Yii::$app->formatter->asCurrency($model->balance_initial) ?>
    </div>
    <div class="col-sm-6 text-center">
        <strong><?= Yii::t('accounting', 'Final Balance'); ?></strong>
        <br/>
        <?= Yii::$app->formatter->asCurrency($model->balance_final) ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">&nbsp;</div>
</div>
<?php
/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Conciliation */
if ($resumeItemsDebitDataProvider) {
    $cols = [];
    if (!$readOnly){
        $cols[] = [
            'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' => function($model, $key, $index, $column) {
                return ['value' => $model->resume_item_id];
            }
        ];
    }

    $cols = array_merge($cols, [
        [
            'header'=>Yii::t('app', 'Date'),
            'attribute'=>'date',
            'format'=>['date']
        ],
        [
            'header'=>Yii::t('app', 'Description'),
            'attribute'=>'description',
        ],

        [
            'header'=>Yii::t('app', 'Debit'),
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model->debit);
            }
        ],
        [
            'header'=>Yii::t('app', 'Status'),
            'value' => function ($model) {
                return Yii::t('accounting', ucfirst($model->status));
            }
        ],
    ]);

    echo GridView::widget([
        //'layout'=> '{items}',
        'id'=> 'w_resume_items_debit',
        'dataProvider' => $resumeItemsDebitDataProvider,
        'columns' => $cols
    ]);

}

if ($resumeItemsCreditDataProvider) {
    $cols = [];
    if (!$readOnly){
        $cols[] = [
            'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' => function($model, $key, $index, $column) {
                return ['value' => $model->resume_item_id];
            }
        ];
    }

    $cols = array_merge($cols, [
        [
        'header'=>Yii::t('app', 'Date'),
        'attribute'=>'date',
        'format'=>['date']
        ],
        [
            'header'=>Yii::t('app', 'Description'),
            'attribute'=>'description',
        ],

        [
            'header'=>Yii::t('app', 'Credit'),
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model->credit);
            }
        ],
        [
            'header'=>Yii::t('app', 'Status'),
            'value' => function ($model) {
                return Yii::t('accounting', ucfirst($model->status));
            }
        ]]);

    echo GridView::widget([
        //'layout'=> '{items}',
        'id'=> 'w_resume_items_credit',
        'dataProvider' => $resumeItemsCreditDataProvider,
        'columns' => $cols
    ]);
}
Pjax::end();