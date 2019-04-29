<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\provider\models\search\ProviderBillSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="row">
    <div class="col-md-12">
        <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'table-responsive'],
                'columns' => [
                    'description',
                    'amount:currency'
                ]
        ]); ?>
    </div>
</div>