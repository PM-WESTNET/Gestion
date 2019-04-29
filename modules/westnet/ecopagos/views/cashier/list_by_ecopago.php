<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\westnet\ecopagos\EcopagosModule;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = EcopagosModule::t('app', 'Cashier list') . ' | ' . $ecopago->name;
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Ecopagos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $ecopago->name, 'url' => ['ecopago/view', 'id' => $ecopago->ecopago_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashier-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . EcopagosModule::t('app', 'Add cashier'), ['cashier/add-cashier', 'ecopago_id' => $ecopago->ecopago_id], ['class' => 'btn btn-success']) ?>        
        </p>
    </div>


    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'cashier_id',
            'address_id',
            [
                'header' => 'Ecopago',
                'value' => function($model) {
                    if (!empty($model->ecopago))
                        return $model->ecopago->name;
                }
            ],
            'name',
            'lastname',
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
