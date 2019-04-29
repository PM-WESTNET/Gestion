<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\ticket\TicketModule;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Ticket Types');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="type-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?=
            Html::a("<span class='glyphicon glyphicon-plus'></span> " . TicketModule::t('app', 'Create Type'), ['create'], ['class' => 'btn btn-success'])
            ;
            ?>
        </p>
    </div>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'type_id',
            'user_group_id',
            'name',
            'description:ntext',
            'slug',
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]);
    ?>

</div>
