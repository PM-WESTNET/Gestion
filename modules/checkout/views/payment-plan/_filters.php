<?php

use app\modules\sale\models\Customer;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\jui\DatePicker;


?>


<div class="payment-plan-filters">
    <?php
    error_log('renderizando filtros');
    $form = ActiveForm::begin(['method' => 'GET']) ?>
    <div class="row">
        <div class="col-lg-6">

            <?=
            $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form'=> $form, 'model' => $search, 'attribute' => 'id_customer'])
            ?>

        </div>

        <div class="col-lg-6">

            <?=
            $form->field($search, 'from_date')->widget(DatePicker::className(), [
                'dateFormat' => 'dd-MM-yyyy',
                'clientOptions' => [
                    'yearRange' => '-10:+100',
                    'changeYear' => true],
                'options' => ['class' => 'form-control', 'id' => 'startDate', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Indique la fecha de comienzo del contrato']
            ])
            ?>

        </div>

        <div class="row">
            <div class="col-lg-1">
                <?= Html::submitButton('Filtrar', ['class' => 'btn btn-primary'])?>
            </div>
            <div class="col-lg-1">
                <?= Html::a('Borrar Filtros', Url::to(['checkout/payment-plan/list']), ['class' => 'btn btn-warning'])?>
            </div>


        </div>

        <?php ActiveForm::end();?>

    </div>



</div>

