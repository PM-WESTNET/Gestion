<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Resume */

$this->title = $model->moneyBoxAccount->moneyBox->name . " - " . $model->moneyBoxAccount->number . ' - ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Resumes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resume-view">

   <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>

            <?php if ($model->status=='draft') { ?>
                    <!-- <div class="btn-group" role="group"> -->
                        <?=Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->resume_id], ['class' => 'btn btn-primary']);?>
                    <!-- </div>
                    <div class="btn-group" role="group"> -->
                        <?=Html::a("<span class='glyphicon glyphicon-eye-open'></span> " . Yii::t('accounting', 'Edit Detail'), ['details', 'id' => $model->resume_id], ['class' => 'btn btn-view']);?>
                    <!-- </div> -->
            <?php } ?>

            <?php if($model->can('closed')) {

                echo Html::a("<span class='glyphicon glyphicon-ok'></span> " . Yii::t('app', 'Ready'), ['change-state', 'id' => $model->resume_id, 'newState' => 'closed'], [
                    'class' => 'btn btn-success',
                    'data' => [
                        'confirm' => Yii::t('accounting', 'Are you sure you want to close this resume ?'),
                        'method' => 'post',
                    ],]);
            }
            ?>

            <?php if($model->can('canceled')) {
                echo Html::a(Yii::t('app', 'Cancel'), ['change-state', 'id' => $model->resume_id, 'newState' => 'canceled'], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => Yii::t('accounting', 'Are you sure you want to cancel this resume ?'),
                        'method' => 'post',
                    ],]);
            }
            ?>

            <?php if($model->getDeletable()) {
                 echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->resume_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]);
            }
            ?>
        </p>
   </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'label' => Yii::t('accounting', 'Money Box Account'),
                'attribute' => function ($model) {
                    return $model->moneyBoxAccount->moneyBox->name . " - " . $model->moneyBoxAccount->number;
                }
            ],
            'date',
            'date_from',
            'date_to',
            'balance_initial:currency',
            'balance_final:currency',
            [
                'label' => Yii::t('app', 'Status'),
                'attribute' => function ($model) {
                    return Yii::t('accounting', ucfirst($model->status));
                }
            ]
        ],
    ]) ?>

</div>

<div class="row">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <strong>
                <?= Yii::t('accounting', 'Resume Detail') ?>
            </strong>
        </div>
        <div class="panel-body">
            <div class="row">
                <?php
                $resumeItems = new  \yii\data\ActiveDataProvider(['query'=>$model->getResumeItems()]);
                echo GridView::widget([
                    'layout' => '{items}',
                    'id' => 'wSummary',
                    'dataProvider' => $resumeItems,
                    'columns' => [
                        [
                            'header' => Yii::t('app', 'Date'),
                            'attribute' => 'date',
                            'format' => ['date']
                        ],
                        [
                            'header' => Yii::t('accounting', 'Operation Type'),
                            'value' => function ($model){
                                return $model->moneyBoxHasOperationType->operationType->name;
                            },
                        ],
                        [
                            'header' => Yii::t('app', 'Description'),
                            'attribute' => 'description',
                        ],
                        [
                            'header' => Yii::t('accounting', 'Debit'),
                            'value' => function ($model) {
                                return Yii::$app->formatter->asCurrency($model->debit);
                            }
                        ],
                        [
                            'header' => Yii::t('accounting', 'Credit'),
                            'value' => function ($model) {
                                return Yii::$app->formatter->asCurrency($model->credit);
                            }
                        ],
                        [
                            'header' => Yii::t('app', 'Status'),
                            'value' => function ($model) {
                                return Yii::t('accounting', ucfirst($model->status));
                            }
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>