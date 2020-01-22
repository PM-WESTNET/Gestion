<?php

$this->title = $account->name . ' - '. Yii::t('app', 'Mayor Book');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="mayor-book">

    <h1><?php echo $this->title ?></h1>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo Yii::t('app', 'Filters')?></h3>
        </div>
        <div class="panel-body">
            <?php $form = ?>
        </div>
    </div>

    <?php

        echo \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'created_at',
                    'label' => Yii::t('app', 'Date'),
                    'value' => function ($model) {
                        return Yii::$app->formatter->asDate($model['created_at'], 'dd-MM-yyyy');
                    }
                ],
                [
                    'attribute' => 'description',
                    'label' => Yii::t('app', 'Description'),
                ],
                [
                    'attribute' => 'debit',
                    'label' => Yii::t('app', 'Debit'),
                    'format' => 'currency'
                ],
                [
                    'attribute' => 'credit',
                    'label' => Yii::t('app', 'Credit'),
                    'format' => 'currency'
                ],
                [
                    'attribute' => 'balance',
                    'label' => Yii::t('app', 'Balance'),
                    'format' => 'currency'
                ],

                [
                    'class' => \app\components\grid\ActionColumn::class,
                    'buttons' => [
                        'view' => function($url, $model) {
                            return \app\components\helpers\UserA::a('<span class="glyphicon glyphicon-eye-open"></span>',
                                \yii\helpers\Url::to(['account-movement/view', 'id' => $model['account_movement_id']]),
                                [
                                    'class' => 'btn btn-success'
                                ]
                            );
                        }
                    ],
                    'template' => '{view}'
                ]
            ]
        ])

    ?>

</div>

