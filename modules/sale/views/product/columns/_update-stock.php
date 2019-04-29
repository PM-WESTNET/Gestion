<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\config\models\Config;

return [
    ['class' => 'yii\grid\SerialColumn'],
    ['class' => yii\grid\CheckboxColumn::className()],
    //'product_id',
    [
        'attribute'=>'name',
        'content' => function($model, $key, $index, $column){
            //Nombre
            $content = $model->name;

            if(Yii::$app->params['categories-location'] == 'name'){
                $content .= '<br/>';
                foreach($model->categories as $i=>$category){
                    if($i > 0){
                        $content .= ', ';
                    }
                    $content .= "<a href='#' onclick='Search.addCategory($category->category_id)'>$category->name</a>";
                    //$content .= Html::a($category->name, ['product/index','ProductSearch'=>['categories'=>[$category->category_id] ] ] );
                }
            }
            return $content;
        },
    ],
    [
        'attribute'=>'code',
        'visible'=>Yii::$app->params['show-code-column']
    ],
    [
        'attribute' => 'stock',
        'value' => function($model) use ($searchModel){
            $company = app\modules\sale\models\Company::findOne($searchModel->stock_company_id);
                
            $secondaryStock = $model->getSecondaryStock($company, true);
            if($secondaryStock){
                return $model->getStock($company, true).' | '.$secondaryStock;
            }
            return $model->getStock($company, true);
        },
        'contentOptions' => ['data-update' => 'combined_stock']
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'label'=>Yii::t('app','Incoming movement'),
        'format'=>['raw'],
        'value' => function($model, $key, $index, $column){
        
            $secondaryStock = Config::getValue('enable_secondary_stock') && $model->secondaryUnit;
        
            //csrf
            $html = Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken);
            
            //company_id
            $html .= Html::hiddenInput('StockMovement[company_id]', $column->grid->filterModel->stock_company_id);
            $html .= Html::hiddenInput('StockMovement[concept]', Yii::t('app', 'Quick update'));
    
            //Input stock primario
            $html .= Html::tag('div', app\modules\sale\components\StockInput::widget([
                'name' => 'StockMovement[qty]',
                'unit' => $model->unit,
                'options' => [
                    'class' => $secondaryStock ? 'form-control input-sm' : 'form-control submit-input input-sm', 
                    'data-tabupdate' => $secondaryStock ? 'no' : 'yes', 
                    'id' => 'input-qty'.$index, 
                    'tabindex' => ($index*2)+1,
                    'placeholder' => $model->unit->name
                ]
            ]), ['class' => $secondaryStock ? 'col-md-4' : 'col-md-8']);

            //Input stock secundario
            if($secondaryStock){
                $html .= Html::tag('div', app\modules\sale\components\StockInput::widget([
                    'unit' => $model->secondaryUnit,
                    'name' => 'StockMovement[secondary_qty]',
                    'options' => [
                        'class' => 'form-control submit-input input-sm', 
                        'data-tabupdate' => 'yes', 
                        'id' => 'input-secondary-qty'.$index, 
                        'tabindex' => ($index*2)+2,
                        'placeholder' => $model->secondaryUnit->name
                    ]
                ]), ['class' => 'col-md-4']);
            }
            
            //Boton para guardar
            $html .= Html::tag('div', 
                    Html::submitButton('<span class="glyphicon glyphicon-plus"></span>', ['class' => 'btn btn-success btn-sm', 'style' => 'width: 100%;', 'form' => 'form'.$index]), 
                    ['class' => 'col-md-2']);
            
            //Boton para guardar
            $html .= Html::tag('div', 
                    Html::resetButton('<span class="glyphicon glyphicon-remove"></span>', ['class' => 'btn btn-default btn-sm', 'style' => 'width: 100%;', 'form' => 'form'.$index]), 
                    ['class' => 'col-md-2']);
            
            //Row
            $html = Html::tag('div', $html, ['class' => 'row', 'style' => 'min-width: 300px;']);
            
            //Help-block para mostrar mensajes y errores
            $html .= Html::tag('div', Html::tag('div', null, ['class' => 'help-block', 'data-messages'=>'']));
            
            //Tag form
            $form = Html::tag('form', 
                    $html, 
                    [
                        'action' => yii\helpers\Url::to(['update-stock', 'product_id' => $model->product_id]),
                        'method' => 'get',
                        'id' => 'form'.$index,
                        'data-column-updater' => ''
                    ]);
            
            return $form;
        }
    ],
    [
        'label'=>Yii::t('app','Last incoming movement'),
        'value'=>function($model, $key, $index, $column){
            $sm = \app\modules\sale\models\StockMovement::find()->where(['product_id'=>$model->product_id])->orderBy(['timestamp'=>SORT_DESC])->one();
            return empty($sm) ? '' : Yii::$app->formatter->asDate($sm->date);
        },
        'contentOptions' => ['data-update' => 'date']
    ],
    [
        'label'=>Yii::t('app','Categories'),
        'content' => function($model, $key, $index, $column){
            $content = '';
            foreach($model->categories as $i=>$category){
                if($i > 0){
                    $content .= ', ';
                }
                $content .= Html::a($category->name, ['product/index','ProductSearch'=>['categories'=>[$category->category_id] ] ] );
            }
            return $content;
        },
        'visible'=>Yii::$app->params['categories-location'] == 'column' ? true : false
    ],
    [
        'class' => 'app\components\grid\ActionColumn',
        'template' => '{price-history}',
        'buttons' => [
            'price-history'=>function($url, $model){
                return Html::a('<span class="glyphicon glyphicon-tags"></span>', $url, [
                    'title' => Yii::t('yii', 'Price history'),
                    'target'=>'_blank',
                    'data-pjax' => '0',
                ]);
            }
        ],
        'header'=>Yii::t('app','History')
    ],
    'description:ntext',
    [
        'class' => 'app\components\grid\ActionColumn',
        'template' => '{view} {update}',
    ],
];
