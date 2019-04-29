<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\bootstrap\Modal;

return [
    'class' => '\yii\grid\DataColumn',
    'label'=>Yii::t('app','Unit Amount'),
    'format'=>['raw'],
    'visible' => Yii::$app->params['bill_detail_price_updater'],
    'value' => function($model, $key, $index, $column){

        //csrf
        $html = Html::hiddenInput('model_id', $model->bill_detail_id);

        $input = Html::tag('span', '$', ['class' => 'input-group-addon']).Html::textInput('BillDetail[unit_net_price]',
            Yii::$app->formatter->asDecimal((empty($model->unit_net_price) ? 0 : $model->unit_net_price), 2),
            [
                'class' => 'form-control submit-input input-sm', 
                'data-tabupdate' => 'yes', 
                'id' => 'input-prc'.$index, 
                'tabindex' => ($index*3)+3,
                'data-update' => 'unit_net_price'
            ]);

        $input = Html::tag('div', $input, ['class' => 'input-group']);

        //Input 
        $html .= Html::tag('div', $input, ['class' => 'col-md-12']);

        //Row
        $html = Html::tag('div', $html, ['class' => 'row', 'style' => 'min-width: 100px;']);

        //Help-block para mostrar mensajes y errores
        $html .= Html::tag('div', Html::tag('div', null, ['class' => 'help-block', 'data-messages'=>'unit_net_price']));

        //Tag form
        $form = Html::tag('div', 
                $html, 
                [
                    'data-action' => yii\helpers\Url::to(['bill/update-qty']),
                    'id' => 'form'.$index,
                    'data-column-updater' => '',
                    'style' => 'max-width: 200px;'
                ]);

        return $form;
    }
];