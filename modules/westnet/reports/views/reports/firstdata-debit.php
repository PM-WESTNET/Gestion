<?php

use dosamigos\chartjs\ChartJs;

$this->title = Yii::t('app', 'Firstdata Automatic Debit Report');
?>


<div class="firstdata-report">
    <h1 class="title">
        <?= $this->title ?>
    </h1>

    <?= ChartJs::widget([
            'type' => 'line',
            'options' => [
                'height' => 400,
                'width' => 400
            ],
            'data' => $data
        ]);
    ?>

</div>