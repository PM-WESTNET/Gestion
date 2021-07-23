<?php
use yii\grid\SerialColumn;
$this->title = Yii::t('app','Discounts');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    
    <h1>Filtrar Por</h1>
    <div class="row">
        <div class="col-md-6">
            <div class="col-md-12">
                <h1>Descuentos</h1>
                <?php echo \yii\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $discountSearch,
                    'columns' => [
                        ['class' => SerialColumn::class],
                        [
                            'attribute' => 'name',
                            'format' => 'text',
                            'label' => 'Descripcion',
                        ],
                        [
                            'attribute' => 'customerAmount',
                            'format' => 'text',
                            'label' => 'Cant Clientes',
                            'value' => function ($model) {
                                return $model->customerAmount;
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'text',
                            'label' => 'Estado',
                        ],
                        [
                            'attribute' => 'value',
                            'format' => 'text',
                            'label' => 'Valor',
                        ],
                        [
                            'attribute' => 'from_date',
                            'format' => 'text',
                            'label' => 'Desde',
                        ],
                        [
                            'attribute' => 'to_date',
                            'format' => 'text',
                            'label' => 'Hasta',
                        ],
                    ]
                ])?>        
            </div>
        </div>
        <div class="col-md-6">
            <div class="col-md-12">
            
                <h1>Clientes</h1>
                

            </div>

        </div>

    </div>
</div>

