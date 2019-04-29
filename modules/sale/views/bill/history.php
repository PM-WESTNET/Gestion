<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\BillSearch $searchModel
 */

$this->title = Yii::t('app', 'Bills summary');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bill-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    
    $item = '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters');
    
    echo \yii\bootstrap\Collapse::widget([
        'items' => [
            [
                'label' => $item,
                'content' => $this->render('_search-history', ['model' => $searchModel]),
                'encode' => false,
            ],
        ]
    ]);
    
    ?>

    <?php 
    //Debemos mostrar el grafico?
    if($searchModel->chartType != false && $dataProvider->count > 1): ?>
        <?= dosamigos\chartjs\ChartJs::widget([
            'type' => $searchModel->chartType,
            'options' => [
                'class' => 'graph-responsive'
            ],
            'data' => [
                'labels' => $graphData->getSteps(),
                'datasets'=>$graphData->getDatasets()
            ]
        ]);
        ?>
        <hr/>
    <?php 
    //Si no hay mas de un dato, no se puede generar el grafico
    elseif($searchModel->chartType != false && $dataProvider->count <= 1): ?>
        <?= yii\bootstrap\Alert::widget([
            'options' => [
                'class' => 'alert-info',
            ],
            'body' => Yii::t('app','In order to render a chart, more than one row of data is needed.'),
        ]); ?>
        <hr/>
    <?php endif; ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'filterSelector'=>'.filter',
        'options' => ['class' => 'table-responsive'],        
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'date',
            ],
            [
                'attribute'=>'total',
                'format'=>['currency']
            ],
        ],
    ]); ?>

    <table class="table table-bordered">
        <tr>
            <td>
                <strong><?= Yii::t('app', 'Total for this period'); ?></strong>
            </td>
            <td>
                <strong>
                    <?php
                    //Formatter para currency
                    $formatter = Yii::$app->formatter;
                    echo $formatter->asCurrency($searchModel->periodTotal); ?>
                </strong>
            </td>
        </tr>
    </table>
</div>

<script type="text/javascript">
    
    var Search = new function(){

        //public
        this.windowKeypress = function(e){

            if($(":focus").length == 0 && e.which > 20 && e.which < 127) {

                autoFocus = true;

                $("#search_text").val(String.fromCharCode( e.which ));
                $("#search_text").focus();

            }

        }
        this.clear = function(){

            $.pjax({container: '#grid', url: '<?= yii\helpers\Url::toRoute(['stock-movement/graph']) ?>'});

        }
        
        this.clearDates = function(){
            
            $('.dates').val('');
            $('.dates').change();
            
        }

    }

</script>
