<?php

use app\modules\accounting\models\Account;
use app\modules\paycheck\models\Paycheck;
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
        <div class="col-sm-6">
            <div class="row">
                <?= $form->field($model, 'owned')->checkboxList([
                    1 => Yii::t('paycheck', 'own'),
                    0 => Yii::t('paycheck', 'no_own')
                ], ['separator' => '<br>'])->label(Yii::t('paycheck', 'Origin')) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'crossed')->dropDownList([
                    2 => Yii::t('app', 'All'),
                    1 => Yii::t('paycheck', 'Crossed'),
                    0 => Yii::t('paycheck', 'No Crossed')
                ], ['separator' => '<br>'])->label(Yii::t('paycheck', 'Crossed')) ?>
                </div>
            <div class="row">
                <?= $form->field($model, 'to_order')->dropDownList([
                    2 => Yii::t('app', 'All'),
                    1 => Yii::t('paycheck', 'To Order'),
                    0 => Yii::t('paycheck', 'No To Order')
                ], ['separator' => '<br>'])->label(Yii::t('paycheck', 'To Order')) ?>
            </div>
            <div class="row">
                <?=$form->field($model, 'name')->textInput()
                    ->label(Yii::t('paycheck', 'Business Name/Description'));
                ?>
            </div>
            <div class="row">
                <?=$form->field($model, 'number')->textInput()
                    ->label(Yii::t('paycheck', 'number'));
                ?>
            </div>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'statuses')->checkboxList([
                Paycheck::STATE_CREATED => Yii::t('paycheck', Paycheck::STATE_CREATED),
                Paycheck::STATE_COMMITED => Yii::t('paycheck', Paycheck::STATE_COMMITED),
                Paycheck::STATE_RECEIVED => Yii::t('paycheck', Paycheck::STATE_RECEIVED),
                Paycheck::STATE_CANCELED => Yii::t('paycheck', Paycheck::STATE_CANCELED),
                Paycheck::STATE_CASHED => Yii::t('paycheck', Paycheck::STATE_CASHED),
                Paycheck::STATE_REJECTED => Yii::t('paycheck', Paycheck::STATE_REJECTED),
                Paycheck::STATE_RETURNED => Yii::t('paycheck', Paycheck::STATE_RETURNED),
                Paycheck::STATE_DEPOSITED => Yii::t('paycheck', Paycheck::STATE_DEPOSITED)
            ], ['separator' => '<br>']) ?>
        </div>

        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'fromDate')->widget(yii\jui\DatePicker::className(), [
                        'language' => 'es',
                        'model' => $model,
                        'attribute' => 'fromDate',
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
                        'language' => 'es',
                        'model' => $model,
                        'attribute' => 'toDate',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class' => 'form-control dates',
                            'id' => 'to-date'
                        ]
                    ]);
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'fromDueDate')->widget(yii\jui\DatePicker::className(), [
                        'language' => 'es',
                        'model' => $model,
                        'attribute' => 'fromDueDate',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control dates',
                            'id' => 'from-due-date'
                        ]
                    ]);
                    ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'toDueDate')->widget(yii\jui\DatePicker::className(), [
                        'language' => 'es',
                        'model' => $model,
                        'attribute' => 'toDueDate',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class' => 'form-control dates',
                            'id' => 'to-due-date'
                        ]
                    ]);
                    ?>
                </div>
            </div>

        </div>
    </div>
    
    <hr>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
        <?= Html::a(Yii::t('app', 'Clear'), $form->action, ['class' => 'btn btn-default pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
