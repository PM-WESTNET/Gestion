
<?php

use yii\grid\GridView;
$now = date('d/m/y H:i');
header('Content-type:application/xls');
header("Content-Disposition: attachment; filename=$now-customer-status-quantity.xls");
?>

<?= GridView::widget([
        
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Estado Cliente - Estado Contrato - Estado Conexion - Estado Cuenta Conexion',
                'value' => function($model){
                    return $model['combination'];
                }
            ],
            'quantity'
        ],

    ]); 
    
    ?>