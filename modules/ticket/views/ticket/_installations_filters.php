<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use webvimark\modules\UserManagement\models\User;
use yii\helpers\Url;
use app\modules\ticket\components\schemas\SchemaCobranza;
use yii\jui\DatePicker;
use app\modules\ticket\components\schemas\SchemaInstalaciones;
use app\modules\ticket\models\Ticket;
use app\modules\ticket\TicketModule;

$form= ActiveForm::begin(['method' => 'GET']);
?>


<div class="ticket_filters">
    <div class="row">
        <div class="col-sm-12" style="text-align: right">

            <?php echo $form->field($model, 'show_all')->checkbox()?>
        </div>
    </div>
    <div class="row">

        <div class="col-sm-3">
            <?= $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form' => $form, 'model' => $model, 'attribute' => 'customer_id']) ?>
        </div>

        <div class="col-sm-3">
            <?=$form->field($model, 'status_id')->dropDownList(ArrayHelper::map(SchemaInstalaciones::getSchemaStatuses(), 'status_id', 'name'), ['prompt' => Yii::t('app', 'All')])?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'start_date_from')->widget(DatePicker::class, [
                    'model' => $model,
                    'attribute' => 'start_date_from',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                            'class' => 'form-control'
                    ]
            ])?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'start_date_to')->widget(DatePicker::class, [
                'model' => $model,
                'attribute' => 'start_date_to',
                'dateFormat' => 'yyyy-MM-dd',
                'options' => [
                    'class' => 'form-control'
                ]
            ])?>
        </div>
    </div>
    <div class="row">

        <div class="col-sm-3">
            <?= $form->field($model, 'date_from_start_contract')->widget(DatePicker::class, [
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                            'class' => 'form-control'
                    ]
            ])?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'date_to_start_contract')->widget(DatePicker::class, [
                'dateFormat' => 'yyyy-MM-dd',
                'options' => [
                    'class' => 'form-control'
                ]
            ])?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'ticket_management_qty')->textInput() ?>
        </div>


        <div class="col-sm-3">
            <?=$form->field($model, 'discounted')->dropDownList( [
               'undiscounted' => TicketModule::t('app', 'Undiscounted'),
               'discounted' => TicketModule::t('app', 'Discounted')
            ],
            [
                'prompt'=> Yii::t('app', 'Select an option')
            ])?>
        </div>

    </div>

    <div class="row">

        <div class="col-lg-1">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>

        </div>
        <dvi class="col-lg-1">
            <?= \yii\bootstrap\Html::a('Borrar Filtros', Url::to(['index']), ['class' =>'btn btn-default'])?>
        </dvi>
    </div>


</div>
<?php $form->end()?>
</div>
