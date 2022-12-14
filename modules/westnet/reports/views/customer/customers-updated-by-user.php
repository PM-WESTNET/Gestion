<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use webvimark\modules\UserManagement\models\User;

$this->title = Yii::t('app', 'Updated Customers Report By User');
?>

<div class="customer-updated-by-user">

    <h1><?=$this->title?></h1>

    <?php $form = ActiveForm::begin(['method' => 'POST']); ?>

    <div class="row">
        <div class="col-md-4">
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
        <div class="col-md-4">
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
        <div class="col-md-4">
           <?php echo $form->field($search, 'user_id')->widget(Select2::class, [
               'data' => $users,
               'pluginOptions' => [
                   'allowClear' => true
               ],
               'options' => ['placeholder' => Yii::t('app', 'Select an option...') ]
           ]);?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>


    <?= GridView::widget([
        'dataProvider' => $data,
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