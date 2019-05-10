<?php

use app\modules\accounting\models\Account;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\BillType;
use app\modules\sale\models\Currency;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\search\BillSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="bill-search">

    <?php $form = ActiveForm::begin([
        //'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-5">
            <?= $form->field($model, 'statuses')->checkboxList([
                'draft' => Yii::t('app', 'Draft'),
                'closed' => Yii::t('app', 'Closed'),
                'broken' => Yii::t('accounting', 'Broken')
            ], ['separator' => '<br>']) ?>
        </div>
        <div class="col-sm-7">
            <?= app\components\companies\CompanySelector::widget(['model' => $model, 'inputOptions' => ['prompt' => Yii::t('app', 'All')]]) ?>

            <?= $form->field($model, 'account_id')->widget(Select2::class, [
                'data' => ArrayHelper::map(Account::getForSelect(), 'account_id', 'name' ),
                'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ]);
            ?>

            <?php echo $form->field($model, 'account_movement_id')->textInput()->label(Yii::t('app','Number'))?>

        </div>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'fromDate')->widget(yii\jui\DatePicker::className(), [
                    'language' => Yii::$app->language,
                    'model' => $model,
                    'attribute' => 'date',
                    'dateFormat' => 'dd-MM-yyyy',
                    'options'=>[
                        'class'=>'form-control dates',
                        'id' => 'from-date'
                    ]
                ]);
                ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'toDate')->widget(yii\jui\DatePicker::className(), [
                    'language' => Yii::$app->language,
                    'model' => $model,
                    'attribute' => 'date',
                    'dateFormat' => 'dd-MM-yyyy',
                    'options'=>[
                        'class' => 'form-control dates',
                        'id' => 'to-date'
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
    
    <hr>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning']) ?>
        <?= Html::a(Yii::t('app', 'Clear'), $form->action, ['class' => 'btn btn-info pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
