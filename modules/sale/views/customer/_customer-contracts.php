<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/03/19
 * Time: 11:36
 */

use app\modules\sale\modules\contract\models\search\ContractSearch;
use app\modules\westnet\models\Connection;
use yii\grid\GridView;
use app\components\helpers\UserA;
use yii\helpers\Html;

?>
<hr>
<h2> <?= Yii::t('app', 'Contracts') ?> </h2>

<?= GridView::widget([
    'dataProvider' => $contracts,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'contract_id',
        'from_date',
        [
            'label'=> Yii::t('app', 'Status Account'),
            'value'=>  function($model){
                $con = Connection::findOne(['contract_id' => $model->contract_id]);
                return (!empty($con) ? Yii::t('app', ucfirst($con->status_account). ' Account'): null);
            }
        ],
        [
            'label'=> Yii::t('app', 'Address'),
            'value'=> function($model){
                return $model->address ? $model->address->shortAddress : '';
            }
        ],
        [
            'label' => Yii::t('app', 'Plan'),
            'value' => function ( $model){
                return $model->getPlan()->name;
            }
        ],
        ['class' => 'yii\grid\ActionColumn',
            'template'=>'{view} {update}',
            'buttons'=>[
                'view' => function ($url, $model) {
                    if($model->canView()){
                        return UserA::a('<span class="glyphicon glyphicon-eye-open"></span>',['/sale/contract/contract/view',  'id' => $model->contract_id], [
                            'title' => Yii::t('yii', 'View'),
                            'class' => 'btn btn-view'
                        ]);
                    }
                },
                'update' => function ($url, $model) {
                    if ($model->canUpdate()){
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',['/sale/contract/contract/update',  'id' => $model->contract_id], [
                            'title' => Yii::t('yii', 'Update'),
                            'class' => 'btn btn-primary'
                        ]);
                    }
                },
            ],
        ],
    ],
]);
?>
<hr>
