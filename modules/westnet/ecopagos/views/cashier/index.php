<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Cashiers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashier-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?=
            Html::a("<span class='glyphicon glyphicon-plus'></span> " . \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Create Cashier', [
                        'modelClass' => 'Cashier',
                    ]), ['create'], ['class' => 'btn btn-success'])
            ;
            ?>
        </p>
    </div>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'cashier_id',
            'name',
            'lastname',
            'address_id',
            [
                'header' => 'Ecopago',
                'value' => function($model) {
                    if (!empty($model->ecopago))
                        return $model->ecopago->name;
                }
            ],
            'number',
            // 'document_number',
            // 'document_type',
            // 'username',
            // 'password',
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]);
    ?>

</div>
