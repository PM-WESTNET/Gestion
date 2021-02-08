<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 02/01/19
 * Time: 09:04
 */

use app\components\companies\CompanySelector;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<div class="bill-search">

    <?php $form = ActiveForm::begin([
        //'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($searchModel, 'fromDate')->widget(yii\jui\DatePicker::class, [
                    'language' => Yii::$app->language,
                    'model' => $searchModel,
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

                <?= $form->field($searchModel, 'toDate')->widget(yii\jui\DatePicker::class, [
                    'language' => Yii::$app->language,
                    'model' => $searchModel,
                    'attribute' => 'date',
                    'dateFormat' => 'dd-MM-yyyy',
                    'options'=>[
                        'class' => 'form-control dates',
                        'id' => 'to-date'
                    ]
                ]);
                ?>
            </div>

            <div class="col-md-6">
                <?= CompanySelector::widget(['model' => $searchModel, 'attribute' => 'company_id']) ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary', 'name' => 'submit_search',]) ?>
        <?= Html::submitButton(Yii::t('app', 'Send emails'),  [
                'class' => 'btn btn-success pull-right',
                'name' => 'submit_send',
            'data' => [
                'confirm' => Yii::t('app','Are you sure you want to send these emails? The proccess may take several minutes')
            ]
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

