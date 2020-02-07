<?php

use kartik\export\ExportMenu;
use yii\bootstrap\Collapse;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('accounting', 'Master Book');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-movement-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Collapse::widget([
        'items' => [
            [
                'label' => '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters'),
                'content' => $this->render('_search', ['model' => $searchModel]),
                'encode' => false,
            ],
        ]
    ]);
    $columns = [
        'code',
        'name',
        'debit:currency',
        'credit:currency',
        [
            'label' => Yii::t('accounting', 'Balance'),
            'value' => function($model) {
                return ($model['debit'] - $model['credit']);
            },
            'format' => 'currency'

        ],
        [
            'class' => \app\components\grid\ActionColumn::class,
            'buttons' => [
                'mayor_book' => function ($url, $model) {
                    return \app\components\helpers\UserA::a(Yii::t('app', 'Mayor Book'),
                        \yii\helpers\Url::to(['account-movement/mayor-book', 'account_id' => $model['account_id']]),
                        [
                            'class' => 'btn btn-info'
                        ]
                    );
                }
            ],
            'template' => '{mayor_book}'
        ]
    ];
    // Renders a export dropdown menu
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],        
        'columns' => $columns,
        'showConfirmAlert'=>false
    ]);

    $grid = GridView::begin([
        'id'=>'grid',
        'dataProvider' => $dataProvider,
        'export'=>false,
        'responsive'=>false,
        'options' => ['class' => 'table-responsive'],                
        'columns' => $columns,
    ]);

    $grid->end();
    ?>

    <table class="table table-bordered">
        <tr>
            <td>
                <strong><?= Yii::t('app', 'Total for this period'); ?></strong>
            </td>
            <td>
                <strong>
                    <?php
                    //Formatter para currency
                    $formatter = Yii::$app->formatter;
                    echo $formatter->asCurrency($searchModel->totalDebit); ?>
                </strong>
            </td>
            <td>
                <strong>
                    <?php
                    //Formatter para currency
                    echo $formatter->asCurrency($searchModel->totalCredit); ?>
                </strong>
            </td>
        </tr>
    </table>
</div>
