<?php
use app\modules\westnet\reports\search\CustomerSearch;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Updated Customers Report');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="updated-customers">

    <h1><?php echo $this->title?></h1>
    <hr>

    <?php $form = ActiveForm::begin(['method' => 'POST']); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?= $form->field($search, 'date_from')->widget(DatePicker::class, [
                    'language' => Yii::$app->language,
                    'model' => $search,
                    'attribute' => 'date_from',
                    'dateFormat' => 'dd-MM-yyyy',
                    'options'=>[
                        'class'=>'form-control filter dates',
                        'placeholder'=>Yii::t('app','Date')
                    ]
                ])?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?= $form->field($search, 'date_to')->widget(DatePicker::class, [
                    'language' => Yii::$app->language,
                    'model' => $search,
                    'attribute' => 'date_to',
                    'dateFormat' => 'dd-MM-yyyy',
                    'options'=>[
                        'class'=>'form-control filter dates',
                        'placeholder'=>Yii::t('app','Date')
                    ]
                ])?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

    <?php

        $max = max(array_map(function($o){return $o['y'];}, $data['points']))+ 1;
        $stepSize = 1;

        if ($max > 10 && $max < 20) {
            $stepSize = 2;
        }

        if ($max > 20 && $max < 50 ){
            $stepSize = $max / 10;
        }

        if ($max > 100 && $max < 200){
            $stepSize = 25;
        }

        if ($max > 200 && $max < 1200) {
            $stepSize = 100;
        }

        if ($max > 1200) {
            $stepSize = $max / 10;
        }

        echo \dosamigos\chartjs\ChartJs::widget([
            'type' => 'line',
            'options' => [
                'height' => '50px',
                'width' => '100%',
            ],
            'clientOptions' => [
                'scales' => [
                    'yAxes' => [
                        [
                            'ticks' => [
                                'max' => $max,
                                'stepSize' => $stepSize
                            ]
                        ]
                    ]
                ]
            ],
            'data' => [
                'labels' => $data['labels'],
                'datasets' => [
                    [
                        'label' => Yii::t('app', 'Updated Customers'),
                        'data' => $data['points'],
                        'fill' => false,
                        'lineTension' => 0.1,
                        'borderColor' => sprintf('rgba(%s,%s,%s,0.6)', 255, 80, 80),
                        'borderCapStyle' => 'round',
                        'borderDash' => [],
                        'curveType' => 'none',
                        //'backgroundColor' => sprintf('rgba(%s,%s,%s,0.6)', 255, 80, 80),
                        'backgroundColor' => 'white',

                    ]
                ]
            ]
        ])

    ?>



</div>

<script>

    var UpdatedCustomers = new function() {

        this.init= function () {
            $('#range-drop').on('change', function(e) {
                $('#range-form').submit();
            })
        }
    };

</script>

<?php $this->registerJs('UpdatedCustomers.init()')?>