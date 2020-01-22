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
            <?php $form =  \yii\bootstrap\ActiveForm::begin(['method' => 'GET'])?>
                <div class="row">
                    <div class="col-lg-6">
                        <?php echo $form->field($search, 'fromDate')->widget(\kartik\date\DatePicker::class,[
                            'pluginOptions' => [
                                'format'  => 'dd-mm-yyyy',
                                'autoclose' => true
                            ]
                        ])?>
                    </div>
                    <div class="col-lg-6">
                        <?php echo $form->field($search, 'toDate')->widget(\kartik\date\DatePicker::class,[
                            'pluginOptions' => [
                                'format'  => 'dd-mm-yyyy',
                                'autoclose' => true
                            ]
                        ])?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <?php echo \yii\helpers\Html::submitButton(Yii::t('app', 'Filter'), ['class' => 'btn btn-primary'])?>
                    </div>
                </div>
            <?php \yii\bootstrap\ActiveForm::end()?>
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

