<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;
use yii\data\ArrayDataProvider;
use webvimark\modules\UserManagement\models\User;

$this->title = Yii::t('app', 'Updated Customers Report By User');
?>

<div class="customer-updated-by-user">

    <h1><?=$this->title?></h1>

    <?php $form = ActiveForm::begin(['method' => 'POST']); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?= $form->field($search, 'date_from')->widget(DatePicker::class, [
                    'language' => Yii::$app->language,
                    'model' => $search,
                    'attribute' => 'date_from',
                    'dateFormat' => 'dd-MM-yyyy',
                    'options'=>[
                        'class'=>'form-control filter dates',
                        'placeholder'=>Yii::t('app','Date')
                    ]
                ])?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?= $form->field($search, 'date_to')->widget(DatePicker::class, [
                    'language' => Yii::$app->language,
                    'model' => $search,
                    'attribute' => 'date_to',
                    'dateFormat' => 'dd-MM-yyyy',
                    'options'=>[
                        'class'=>'form-control filter dates',
                        'placeholder'=>Yii::t('app','Date')
                    ]
                ])?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>


    <?= GridView::widget([
        'dataProvider' => (new ArrayDataProvider(['models' => $data->all()])),
        'columns' => [
            [
                'label' => Yii::t('app', 'Users'),
                'value' => function($model) {
                    $user = User::findOne($model['user_id']);

                    if ($user) {
                        return $user->username;
                    }
                }
            ], 
            [
                'label' => Yii::t('app', 'Customers Updated'),
                'value' => function ($model) {
                    return $model['count'];
                }
            ]
        ]
    ])?>


</div>