<?php
use yii\helpers\Html;
use yii\grid\GridView;

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
        'class' => '\yii\grid\DataColumn',
        'label'=>Yii::t('app','Price'),
        'format'=>['raw'],
        'value' => function($model, $key, $index, $column){
        
            //csrf
            $html = Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken);
            
            //Input precio neto
            $html .= Html::tag('div', Html::textInput('ProductPriceForm[net]',
                null,
                [
                    'class' => 'form-control submit-input input-sm', 
                    'data-tabupdate' => 'yes', 
                    'id' => 'input-net'.$index, 
                    'tabindex' => $index+1,
                    'placeholder' => Yii::t('app', 'Net') . ($model->netPrice ? ' : '.Yii::$app->formatter->asCurrency($model->netPrice) : ''),
                    'data-update' => 'netPrice',
                    //Se actualiza el neto o el final, no ambos:
                    'oninput' => "$('#input-final$index').val('')"
                ]
            ), ['class' => 'col-md-4']);

            //Input precio final
            $html .= Html::tag('div', Html::textInput('ProductPriceForm[final]',
                null,
                [
                    'class' => 'form-control submit-input input-sm', 
                    'data-tabupdate' => 'yes', 
                    'id' => 'input-final'.$index, 
                    'placeholder' => Yii::t('app', 'Final') . ($model->finalPrice ? ' : '.Yii::$app->formatter->asCurrency($model->finalPrice) : ''),
                    'data-update' => 'finalPrice',
                    //Se actualiza el neto o el final, no ambos:
                    'oninput' => "$('#input-net$index').val('')"
                ]
            ), ['class' => 'col-md-4']);
            
            //Boton para guardar
            $html .= Html::tag('div', 
                    Html::submitButton('<span class="glyphicon glyphicon-ok"></span>', ['class' => 'btn btn-primary btn-sm', 'style' => 'width: 100%;', 'form' => 'form'.$index]), 
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
                        'action' => yii\helpers\Url::to(['update-price', 'product_id' => $model->product_id]),
                        'method' => 'get',
                        'id' => 'form'.$index,
                        'data-column-updater' => ''
                    ]);
            
            return $form;
        }
    ],
    [
        'label'=>Yii::t('app','Last price update'),
        'value'=>function($model, $key, $index, $column){
            $price = $model->activePrice;
            if(empty($price)){
                return null;
            }else{
                $date = Yii::$app->formatter->asDate($price->date);
            }
            if($price->timestamp < (time()- ((int)Yii::$app->params['update-price-warning']*60*60*24) )){
                return "<span style='color: orange;'> $date </span>";
            }else{
                return $date;
            }
        },
        'format'=>'html',
        'contentOptions' => ['data-update' => 'priceDate']
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
