<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\bootstrap\Modal;

return [
    'class' => '\yii\grid\DataColumn',
    'label'=>Yii::t('app','Quantity'),
    'format'=>['raw'],
    'value' => function($model, $key, $index, $column){

        $secondaryStock = \app\modules\config\models\Config::getValue('enable_secondary_stock') && ($model->product ? $model->product->secondary_unit_id : false ) ;

        //csrf
        $html = Html::hiddenInput('model_id', $model->bill_detail_id);

        //Input stock primario
        $html .= Html::tag('div', app\modules\sale\components\StockInput::widget([
            'name' => 'BillDetail[qty]',
            'unit' => ($model->product ? $model->product->unit : $model->unit),
            'options' => [
                'class' => $secondaryStock ? 'form-control input-sm' : 'form-control submit-input input-sm', 
                'data-tabupdate' => $secondaryStock ? 'no' : 'yes', 
                'id' => 'input-qty'.$index, 
                'tabindex' => ($index*3)+1,
                'data-update' => 'qty'
            ],
            'value' => $model->qty
        ]), ['class' => $secondaryStock ? 'col-md-6' : 'col-md-12']);

        //Input stock secundario
        if($secondaryStock){
            $html .= Html::tag('div', app\modules\sale\components\StockInput::widget([
                'unit' => $model->product->secondaryUnit,
                'name' => 'BillDetail[secondary_qty]',
                'options' => [
                    'class' => 'form-control submit-input input-sm', 
                    'data-tabupdate' => 'yes', 
                    'id' => 'input-secondary-qty'.$index, 
                    'tabindex' => ($index*3)+2,
                    'data-update' => 'secondary_qty'
                ],
                'value' => $model->secondary_qty
            ]), ['class' => 'col-md-6']);
        }

        //Row
        $html = Html::tag('div', $html, ['class' => 'row', 'style' => 'min-width: 100px;']);

        //Help-block para mostrar mensajes y errores
        $html .= Html::tag('div', Html::tag('div', null, ['class' => 'help-block', 'data-messages'=>'qty'])
                .Html::tag('div', null, ['class' => 'help-block', 'data-messages'=>'secondary_qty']));

        //Tag form
        $form = Html::tag('div', 
                $html, 
                [
                    'data-action' => yii\helpers\Url::to(['bill/update-qty']),
                    'id' => 'form'.$index,
                    'data-column-updater' => '',
                    'style' => 'max-width: 400px;'
                ]);

        return $form;
    }
];