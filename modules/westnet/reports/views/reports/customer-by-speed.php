<?php
use yii\helpers\Html;
use kartik\select2\Select2;

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
                'format' => 'raw',
                'label' => 'Plan',
                'filter'=> Select2::widget([
                    'name' => 'ReportSearch[name_product]',
                    'data' => $list_plan,
                    'options' => ['placeholder' => 'Seleccione un plan...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]),
            ],
        ]
    ])?>


</div>
