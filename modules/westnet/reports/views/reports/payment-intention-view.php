<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = 'Intención de Pago N° '.$model->siro_payment_intention_id;
$this->params['breadcrumbs'][] = ['label' => 'Intenciones de Pago', 'url' => ['reports-company/payment-intention']];
$this->params['breadcrumbs'][] = $this->title;
$url =  Url::toRoute(['result-payment-intention','reference'=>$model->reference, 'id_resultado' => $model->id_resultado]);

$this->registerJs("
    var reference = '$model->reference';
    var id_resultado = '$model->id_resultado';
    var result_table  = document.querySelector('.result-table');
    $('#check-status').click(function(){
        $.ajax({ url: '$url',
            type: 'GET',
            success: function(data) {
                    data = JSON.parse(data);
                    const tr = document.createElement('tr');

                    const tdEstado = document.createElement('td');
                    const tdFechaOperacion = document.createElement('td');
                    const tdFechaRegistro = document.createElement('td');
                    const tdIdOperacion = document.createElement('td');
                    const tdMensajeResultado = document.createElement('td');
                    const tdPagoExitoso = document.createElement('td');
                    const tdRendicion = document.createElement('td');

                    tdEstado.textContent = data['Estado'];
                    tdFechaOperacion.textContent = data['FechaOperacion'];
                    tdFechaRegistro.textContent = data['FechaRegistro'];
                    tdIdOperacion.textContent = data['IdOperacion'];
                    tdMensajeResultado.textContent = data['MensajeResultado'];
                    tdPagoExitoso.textContent = data['PagoExitoso']?'Si':'No';
                    tdRendicion.textContent = data['Rendicion']?data['Rendicion']:'Sin Rendición';

                    tr.appendChild(tdEstado);
                    tr.appendChild(tdFechaOperacion);
                    tr.appendChild(tdFechaRegistro);
                    tr.appendChild(tdIdOperacion);
                    tr.appendChild(tdMensajeResultado);
                    tr.appendChild(tdPagoExitoso);
                    tr.appendChild(tdRendicion);
                    
                    result_table.appendChild(tr);
            }
        });   
    });
");



?>
<div class="payment-intention-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'siro_payment_intention_id',
            [
                'attribute' => 'customer',
                'format' => 'raw',
                'label' => Yii::t('app', 'Customer'),
                'value' => function($model){
                    return Html::a($model->customer->lastname . ' ' . $model->customer->name . ' (' .$model->customer->code . ')', 
                                ['/sale/customer/view', 'id' => $model->customer->customer_id], 
                                ['class' => 'profile-link']);
                }
            ],
            [
                'attribute' => 'company',
                'label' => Yii::t('app','Company'),
                'format' => 'raw',
                'value' => function($model){
                    if(!$model->company_id)
                        return null;
                    return $model->company->name;
                }
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('app','Status'),
                'format' => 'raw',
                'filter'=>[
                    'payed'=>Yii::t('app','Pagado'),
                    'pending'=>Yii::t('app','Pendiente'),
                    'canceled'=>Yii::t('app','Cancelado'),
                    'error'=>Yii::t('app','Error'),
                ],
                'value' => function($model){
                    if($model->status == 'payed')
                        return '<span class="label label-success">Pagado</span>';

                    else if($model->status == 'pending')
                        return '<span class="label label-warning">Pendiente</span>';
                    
                    else if($model->status == 'canceled')
                        return '<span class="label label-danger">Cancelado</span>';
                    else
                        return '<span class="label label-danger">Error</span>';
                }
            ],
            'estado',
            [
                'attribute' => 'payment_id',
                'label' => Yii::t('app','payment'),
                'format' => 'raw',
                'value' => function($model){
                    if(!$model->payment_id && $model->status == 'pending'){
                        return  Html::a('<span class="label label-success">Generar Pago Manual</span>', 
                        ['payment-intention-generate-pay', 'id' => $model->siro_payment_intention_id], 
                        ['class' => 'profile-link']);   
                    }else if(!$model->payment_id)
                        return null;
                    return Html::a('Pago N° '.$model->payment_id , 
                                ['/checkout/payment/view', 'id' => $model->payment_id], 
                                ['class' => 'profile-link']);
                }
            ],
            'createdAt',
            'updatedAt',
            'fecha_registro',
            'fecha_operacion',

        ],
    ]) ?>

    <?php if($model->id_resultado && $model->company_id):?>
        <button type="button" class="btn btn-info" id="check-status">Consultar estado en línea</button>
        <div>
            <h3>Consultar estado de pago:</h3>
            <hr>
            <table class="table table-striped table-bordered">
                    <tr>
                    <th scope="col">Estado</th>
                    <th scope="col">Fecha de Operación</th>
                    <th scope="col">Fecha de Registro</th>
                    <th scope="col">ID de Operación</th>
                    <th scope="col">Resultado</th>
                    <th scope="col">¿Pago Exitoso?</th>
                    <th scope="col">Rendición</th>
                    </tr>
                <tbody class="result-table">

                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="mb-2 mt-2">
            <h5>* No se puede consultar el estado en línea de la intención de pago, porque el usuario no ha continuado con el proceso de la misma. </h5>
        </div>
       <?php endif ?>
    <div class="form-group text-center" id="buttonIndex">   
              
        <?=Html::a('<span class="fa fa-reply"></span> Volver', ['/reports/reports-company/payment-intention'], ['data-pjax' => '0', 'class' => 'btn btn-warning']);?>
             
    </div>

</div>
