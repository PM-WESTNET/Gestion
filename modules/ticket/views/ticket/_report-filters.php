<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use kartik\select2\Select2;
use app\modules\ticket\models\Status;
use yii\helpers\ArrayHelper;
use webvimark\modules\UserManagement\models\User;
use app\modules\ticket\models\Category;

?>
<div class="customer-index">

    <div class="customer-search">
        <?php $form = ActiveForm::begin(['method' => 'GET']); ?>
        <div class="row">
            <div class="col-sm-6">
                <?= Html::activeLabel($model, 'start_date_from'); ?>
                <?= DatePicker::widget([
                    'language' => Yii::$app->language,
                    'model' => $model,
                    'attribute' => 'start_date_from',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                        'class' => 'form-control filter dates',
                        'placeholder' => Yii::t('app', 'Date')
                    ]
                ]);
                ?>
            </div>
            <div class="col-sm-6">
                <?= Html::activeLabel($model, 'start_date_to'); ?>
                <?= DatePicker::widget([
                    'language' => Yii::$app->language,
                    'model' => $model,
                    'attribute' => 'start_date_to',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                        'class' => 'form-control filter dates',
                        'placeholder' => Yii::t('app', 'Date')
                    ]
                ]);
                ?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($model, 'category_id')->widget(Select2::class, [
                    'value' => $model->category_id,
                    'data' => ArrayHelper::map(Category::find()->all(), 'category_id', 'name'),
                    'options' => ['placeholder' => Yii::t('app','Select')],
                    'pluginOptions' => [
                        'allowClear' => 'true',
                    ]
                ]) ?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($model, 'status_id')->widget(Select2::class, [
                        'value' => $model->status_id,
                        'data' => ArrayHelper::map(Status::find()->all(), 'status_id', 'name'),
                        'options' => ['placeholder' => Yii::t('app','Select')],
                        'pluginOptions' => [
                            'allowClear' => 'true',
                        ]
                ]) ?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($model, 'assignations')->widget(Select2::class, [
                    'data' => ArrayHelper::map(User::find()->where(['status' => 1])->all(), 'id', 'username'),
                    'options' => ['placeholder' => Yii::t('app','Select')],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true
                    ],
                ])?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($model, 'created_by')->widget(Select2::class, [
                    'data' => ArrayHelper::map(User::find()->where(['status' => 1])->all(), 'id', 'username'),
                    'options' => ['placeholder' => Yii::t('app','Select')],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($model, 'ticket_management_qty')->textInput() ?>
            </div>



        </div>
        <div class="row">
            <div class="col-md-12">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success pull-right']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>