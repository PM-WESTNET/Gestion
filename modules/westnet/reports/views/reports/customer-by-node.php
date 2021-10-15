<?php
use yii\helpers\Url;
use yii\widgets\ActiveForm;
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 22/11/19
 * Time: 13:08
 */
$this->title = Yii::t('app','Customers By Node');
?>

<div class="customer-by-node">

    <h1><?php echo $this->title ?></h1>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo Yii::t('app','Filters')?></h3>
        </div>
        <div class="panel body">
            <br>
            <?php echo $this->render('_customers-by-node-filters')?>
        </div>
    </div>
    
    <?php echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           'node',
           'total'
        ]
    ])?>


</div>
