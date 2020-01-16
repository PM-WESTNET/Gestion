<?php
use app\modules\westnet\reports\search\CustomerSearch;

$this->title = Yii::t('app', 'Updated Customers Report');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="updated-customers">

    <h1><?php echo $this->title?></h1>
    <hr>

    <?php $form = \yii\bootstrap\ActiveForm::begin(['id' => 'range-form'])?>
    <div class="row">
        <div class="col-lg-12">
            <?php echo $form->field($search, 'range')->dropDownList([
                CustomerSearch::LAST_WEEK_RANGE => Yii::t('app', 'Last Week'),
                CustomerSearch::LAST_MONTH_RANGE => Yii::t('app', 'Last Month'),
                CustomerSearch::LAST_YEAR_RANGE => Yii::t('app', 'Last Year'),
            ], ['id' => 'range-drop'])?>
        </div>
    </div>
    <?php \yii\bootstrap\ActiveForm::end()?>

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
                'height' => '100%',
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
                        'backgroundColor' => sprintf('rgba(%s,%s,%s,0.6)', 255, 80, 80),
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