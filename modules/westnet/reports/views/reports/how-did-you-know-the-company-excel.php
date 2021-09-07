
<?php
use yii\grid\GridView;

header('Content-type:application/xls');
header('Content-Disposition: attachment; filename=customer_registrations.xls');
?>

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
                },
                
            ],
            [
                'attribute' => 'total_client',
                'format' => 'raw',
                'label' => Yii::t('app', 'Total'),
                'value' => function($model){
                	return $model->total_client;
                }
            ],
        ],

    ]); 
    
    ?>