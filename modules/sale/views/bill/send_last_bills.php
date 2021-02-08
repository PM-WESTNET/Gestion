<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 28/12/18
 * Time: 17:00
 */

use yii\grid\GridView;
use yii\helpers\Html;
?>

<?php $this->title = Yii::t('app', 'Send emails with bills');?>

<div>
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_search-last-bills', [
        'searchModel' => $searchModel
    ]);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'number',
            [
                'attribute' => 'bill_type_id',
                'value' => function($model){
                    return $model->billType->name;
                }
            ],
            [
                'attribute' => 'customer_id',
                'value' => function($model){
                    return $model->customer->fullName;
                }
            ],
            'date'
        ]
    ]);?>
</div>

