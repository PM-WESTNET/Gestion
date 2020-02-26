<?php
use app\modules\employee\models\Employee;
use kartik\widgets\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

$employee_id = ($attribute ? $attribute : 'employee_id' );

$customer = Employee::findOne($model->$employee_id);
$name = empty($model->$employee_id) ? '' : $customer->name;

echo $form->field($model, $employee_id)->label(isset($label) ? $label : null)->widget(Select2::classname(), [
    'initValueText' => $name,
    'options' => ['placeholder' => Yii::t('app', 'Search')],
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 3,
        'ajax' => [
            'url' => Url::to(['/employee/employee/find-by-name']),
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {name:params.term, id:$(this).val()}; }')
        ],
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'templateResult' => new JsExpression('function(employee) { return employee.text; }'),
        'templateSelection' => new JsExpression('function (employee) { return employee.text; }'),
        'cache' => true
    ],
]);