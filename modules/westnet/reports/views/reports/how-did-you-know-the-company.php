<?php 
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use app\components\companies\CompanySelector;

$this->title = '¿Cómo conoció la Empresa?';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="payment-intention-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <hr>
    </div>

    <div class="customer-search">
        <?php $form = ActiveForm::begin(['method' => 'POST']); ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <?= CompanySelector::widget([
                        'model' => $reportSearch,
                        'attribute' => 'company_id',
                        'inputOptions' => [
                            'prompt' => Yii::t('app', 'All')
                        ]
                    ])?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::activeLabel($reportSearch, 'date_from'); ?>
                    <?= DatePicker::widget([
                        'language' => Yii::$app->language,
                        'model' => $reportSearch,
                        'attribute' => 'date_from',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control filter dates',
                            'placeholder'=>Yii::t('app','Date')
                        ]
                    ]);
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::activeLabel($reportSearch, 'date_to'); ?>
                    <?= DatePicker::widget([
                        'language' => Yii::$app->language,
                        'model' => $reportSearch,
                        'attribute' => 'date_to',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control filter dates',
                            'placeholder'=>Yii::t('app','Date')
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'publicity_shape',
                'format' => 'raw',
                'label' => Yii::t('app', 'Publicity Shape'),
                'value' => function($model){
                	return strtoupper( Yii::t('app', $model->publicity_shape));
                }
            ],
            [
                'attribute' => 'total_client',
                'format' => 'raw',
                'label' => Yii::t('app', 'Total'),
                'value' => function($model){
                	return $model->total_client;
                }
            ],

            
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['reports-company/how-did-you-know-the-company-view-customer', 'publicity_shape' => $model->publicity_shape], ['data-pjax' => '0']);
                    }
                ]   
            ]
        ],

    ]); ?>

</div>