<?php
use yii\grid\SerialColumn;
$this->title = Yii::t('app','Discounts');
?>

<div class="customer-by-node">

    <h1><?php echo $this->title ?></h1>

    <?php echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $discountSearch,
        'columns' => [
            ['class' => SerialColumn::class],
            [
                'attribute' => 'name',
                'format' => 'text',
                'label' => 'Descripcion',
            ],
            [
                'attribute' => 'status',
                'format' => 'text',
                'label' => 'Estado',
            ],
            [
                'attribute' => 'value',
                'format' => 'text',
                'label' => 'Valor',
            ],
            [
                'attribute' => 'from_date',
                'format' => 'text',
                'label' => 'Desde',
            ],
            [
                'attribute' => 'to_date',
                'format' => 'text',
                'label' => 'Hasta',
            ],
        ]
    ])?>


</div>