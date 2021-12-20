<?php
use yii\helpers\Html;
use yii\grid\SerialColumn;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

$this->title = Yii::t('app','Discounts');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    
    <h1>Filtrar Por Descuentos</h1>
    <div class="row">
<!--         <div class="col-md-6">
 -->            <div class="col-md-12">
<!--                 <h1>Descuentos</h1>
 -->                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $discountSearch,
                    'columns' => [
                        ['class' => SerialColumn::class],
                        [
                            'attribute' => 'name',
                            'format' => 'html',
                            'label' => 'Nombre/Descripción',
                            'value' => function($model){
                                return Html::a($model->name, 
                                            ['/sale/discount/view', 'id' => $model->discount_id], 
                                            ['class' => 'profile-link']);
                            },
                            //'contentOptions' => ['style' => 'width:5%;'],
                            'filter' => $list_discount,
                            'filterType' => GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'options' => [
                                    'prompt' => '',
                                    //'multiple' => true, //
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                'toggleAllSettings' => [
                                    'selectLabel' => '',
                                    'unselectLabel' => '',
                                ],
                                
                            ],
                            
                        ],
                        [
                            'attribute' => 'customerAmount',
                            'format' => 'text',
                            'label' => 'Cant. Clientes',
                            'value' => function ($model) {
                                return $model->customerAmount;
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'html',
                            'value' => function ($model) {
                                $labelType = ($model->status == "enabled")? "success" : "danger";
                                return "<span class='label label-$labelType'>$model->status</span>";
                            },
                            'filter'=>['enabled'=>Yii::t('app','Enabled'), 'disabled'=>Yii::t('app','Disabled')],
                        ],
                        [
                            'attribute' => 'value',
                            'format' => 'text',
                            'label' => 'Valor',
                            'value' => function ($model) {
                                return ($model->type == "fixed")? "$".$model->value : $model->value."%";
                            },
                        ],
                        [
                            'attribute'=>'from_date',
                            'format' => 'raw',
                            'value' => function($model) {
                                return $model->from_date . ' - ' . $model->to_date;
                            },
                            'contentOptions' => ['style' => 'width:24%;'],
                            'label' => 'Fechas Desde-Hasta',
                            'filter'=>DateRangePicker::widget([
                                'model' => $discountSearch,
                                'name' => 'createTimeRange',
                                'attribute' => 'from_date',
                                //'value'=>'2011-08-10 to 2020-08-10',
                                'convertFormat' => true,
                                'presetDropdown' => true,
                                'pluginOptions' => [
                                    'timePicker' => false,
                                    //'timePickerIncrement' => 1,
                                    'autoclose' => true,
                                    'locale' => [
                                        'format' => 'Y-m-d',
                                        //'separator'=>' - ',
                                    ],
                                    
                                ]                                
                            ]),  
                        ],
                        [
                            'class' => 'app\components\grid\ActionColumn',
                            'template'=>'{view}',
                            'buttons'=>[
                                'view'=>function ($url, $model, $key) {
                                    return Html::a(
                                        '<span class="glyphicon glyphicon-eye-open updateItem btn btn-primary"></span>',
                                        ['reports/customer-per-discount',
                                        'discount_id' =>  $model->discount_id]
                                    );
                                }
                            ]
                        ],
                    ]
                ])?>        
            </div>
        </div>
        <!-- <div class="col-md-6">
            <div class="col-md-12">
            
                <h1>Clientes</h1>
                

            </div>

        </div> -->

    </div>
</div>

