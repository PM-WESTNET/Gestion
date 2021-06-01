<?php
$this->title = Yii::t('app','Customers By Speed');
?>

<div class="customer-by-node">

    <h1><?php echo $this->title ?></h1>

    <?php echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $reportSearch,
        'columns' => [
            [
                'attribute' => 'code',
                'format' => 'text',
                'label' => 'Código',
            ],
            [
                'attribute' => 'name',
                'format' => 'text',
                'label' => 'Nombre',
            ],
            [
                'attribute' => 'lastname',
                'format' => 'text',
                'label' => 'Apellido',
            ],
            [
                'attribute' => 'name_product',
                'format' => 'text',
                'label' => 'Plan',
            ],
        ]
    ])?>


</div>
