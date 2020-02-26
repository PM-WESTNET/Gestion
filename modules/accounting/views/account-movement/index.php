<?php

use app\modules\accounting\models\AccountMovement;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('accounting', 'Diary Book');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-movement-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
                    'modelClass' => Yii::t('accounting', 'Entry'),
                ]),
                ['create'],
                ['class' => 'btn btn-success'])
            ;?>
        </p>
    </div>

    <?php

    $item = '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters');

    echo \yii\bootstrap\Collapse::widget([
        'items' => [
            [
                'label' => $item,
                'content' => $this->render('_search', ['model' => $searchModel]),
                'encode' => false,
            ],
        ]
    ]);
    ?>
    <?= ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'value' => function($model){
                    $html = '<div class="row"><div class="col-md-8">'. Yii::t('accounting', 'Entry') . " - " .
                        $model['account_movement_id']. " - " . $model['description'] .
                        '</div>';

                    $html .= '<div class="col-md-2"><span class="label label-'.($model['status'] === AccountMovement::STATE_DRAFT ? 'warning' : ($model['status'] ===  AccountMovement::STATE_BROKEN ? 'danger' : 'primary' ) ).'">'.Yii::t('accounting', ucfirst($model['status'])) .'</span></div>';


                    if($model['status'] != 'closed') {
                        $html .= '<div class="col-md-2 text-right">'. Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                Url::toRoute(['account-movement/update', 'id'=>$model['account_movement_id']]), ['class' => 'btn btn-primary']) ;

                        $html .= Html::a('<span class="glyphicon glyphicon-repeat"></span>',
                            Url::toRoute(['account-movement/close', 'id'=>$model['account_movement_id'], 'from'=>'index']), [
                                'title' => Yii::t('yii', 'Close'),
                                'data-confirm' => Yii::t('accounting', 'Are you sure you want to close the entry?'),
                                'data-method' => 'post',
                                'data-pjax' => '1',
                                'class' => 'btn btn-warning',
                            ]);

                        if ($model->getDeletable()) {
                            $html .= Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                    Url::toRoute(['account-movement/delete', 'id'=>$model['account_movement_id']]), [
                                        'title' => Yii::t('yii', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '1',
                                        'class' => 'btn btn-danger',
                                    ]) . '</div>';
                        }
                    }

                    return $html."</div>";
                },
                'format' => 'raw',
                'header'=>Yii::t('app', 'Number'),
                'attribute'=>'account_movement_id',
                'group' => true,
                'groupedRow'=>true,

            ],
            [
                'header'=>Yii::t('app', 'Date'),
                'attribute' => 'date',
                'format'=>['date'],
                'value'=>function ($model, $key, $index, $widget) {
                    return $model['date'];
                },
            ],
            [
                'header'=>Yii::t('accounting', 'Account'),
                'value' => function($model){
                    return ($model['credit'] > 0 ? "&nbsp;&nbsp;&nbsp;&nbsp;" . Yii::t('accounting', 'to') . " " : ""  ) . $model['account'];
                },
                'format' => 'raw'
            ],

            [
                'header'=>Yii::t('accounting', 'Debit'),
                'value' => function ($model) {
                    return ($model['debit'] == 0 ? 0 : $model['debit']) ;
                },
                'format' => ['currency'],
                //'pageSummary' => true,
                'hAlign'=>'right',
            ],
            [
                'header'=>Yii::t('accounting', 'Credit'),
                'value' => function ($model) {
                    return ($model['credit'] == 0 ? 0 : $model['credit']) ;
                },
                'format' => ['currency'],
                //'pageSummary' => true,
                'hAlign'=>'right',
            ],
        ],
    ]);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'value' => function($model){
                    $movement = AccountMovement::findOne($model['account_movement_id']);
                    $html = '<div class="row"><div class="col-md-8">'. Yii::t('accounting', 'Entry') . " - " .
                        $model['account_movement_id']. " - " . $model['description'] .
                        '</div>';

                    $html .= '<div class="col-md-2"><span class="label label-'.($model['status'] === AccountMovement::STATE_DRAFT ? 'warning' : ($model['status'] ===  AccountMovement::STATE_BROKEN ? 'danger' : 'primary' ) ).'">'.Yii::t('accounting', ucfirst($model['status'])) .'</span></div>';


                    if($model['status'] != 'closed') {
                        $html .= '<div class="col-md-2 text-right">';
                        if ($movement->isManualMovement() || ($movement->getUpdatable() && \webvimark\modules\UserManagement\models\User::hasRole('modify-account-movement'))) {
                            $html .= Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                Url::toRoute(['account-movement/update', 'id'=>$model['account_movement_id']]), ['class' => 'btn btn-primary']) ;
                        }

                        $html .= Html::a('<span class="glyphicon glyphicon-repeat"></span>',
                            Url::toRoute(['account-movement/close', 'id'=>$model['account_movement_id'], 'from'=>'index']), [
                                'title' => Yii::t('yii', 'Close'),
                                'data-confirm' => Yii::t('accounting', 'Are you sure you want to close the entry?'),
                                'data-method' => 'post',
                                'data-pjax' => '1',
                                'class' => 'btn btn-warning',
                            ]);

                        if ($movement->getDeletable() && \webvimark\modules\UserManagement\models\User::hasRole('modify-account-movement')) {
                            $html .= Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                    Url::toRoute(['account-movement/delete', 'id'=>$model['account_movement_id']]), [
                                'title' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'data-pjax' => '1',
                                'class' => 'btn btn-danger',
                            ]) ;
                        }

                        $html .= '</div>';
                    }

                    return $html."</div>";
                },
                'format' => 'raw',
                'header'=>Yii::t('app', 'Number'),
                'attribute'=>'account_movement_id',
                'group' => true,
                'groupedRow'=>true,

            ],
            [
                'header'=>Yii::t('app', 'Date'),
                'attribute' => 'date',
                'format'=>['date'],
                'value'=>function ($model, $key, $index, $widget) {
                    return $model['date'];
                },
            ],
            [
                'header'=>Yii::t('accounting', 'Account'),
                'value' => function($model){
                    return ($model['credit'] > 0 ? "&nbsp;&nbsp;&nbsp;&nbsp;" . Yii::t('accounting', 'to') . " " : ""  ) . $model['account'];
                },
                'format' => 'raw'
            ],

            [
                'header'=>Yii::t('accounting', 'Debit'),
                'value' => function ($model) {
                    return ($model['debit'] == 0 ? 0 : $model['debit']) ;
                },
                'format' => ['currency'],
                //'pageSummary' => true,
                'hAlign'=>'right',
            ],
            [
                'header'=>Yii::t('accounting', 'Credit'),
                'value' => function ($model) {
                    return ($model['credit'] == 0 ? 0 : $model['credit']) ;
                },
                'format' => ['currency'],
                //'pageSummary' => true,
                'hAlign'=>'right',
            ],
        ],
    ]); ?>

</div>
