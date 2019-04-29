<?php
use yii\helpers\Html;
use yii\grid\GridView;


return [
        ['class' => 'yii\grid\SerialColumn'],
//      ['class' => yii\grid\CheckboxColumn::className()],
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
//        [
//            'attribute'=>'balance',
//            'filter'=>false,
//            'label'=>Yii::t('app', 'Total stock')
//        ],
        //Para stock de diferentes empresas, usamos stock_company_id
        [
            'filter'=>false,
            'label'=>Yii::t('app', 'Company Stock'),
            'visible'=>$searchModel->stock_company_id != null,
            'value' => function($model) use ($searchModel){
                $company = app\modules\sale\models\Company::findOne($searchModel->stock_company_id);
                
                $secondaryStock = $model->getSecondaryStock($company, true);
                if($secondaryStock){
                    return $model->getStock($company, true).' | '.$secondaryStock;
                }
                return $model->getStock($company, true);
            }
        ],
        [
            'filter'=>false,
            'label'=>Yii::t('app', 'Avaible Stock'),
            'visible'=>$searchModel->stock_company_id != null,
            'value' => function($model) use ($searchModel){
            
                $company = app\modules\sale\models\Company::findOne($searchModel->stock_company_id);
            
                $secondaryAvaibleStock = $model->getSecondaryAvaibleStock($company, true);
            
                if($secondaryAvaibleStock){
                    return $model->getAvaibleStock($company, true).' | '.$secondaryAvaibleStock;
                }
                
                return $model->getAvaibleStock($company, true);
            }
        ],
        [
            'label' => Yii::t('app', 'Stock by company'),
            'visible'=>$searchModel->stock_company_id == null && Yii::$app->params['companies']['enabled'],
            'format'=>'html',
            'value'=>function($model){
                $table = '<table class="table">';
                $companies = app\modules\sale\models\Company::find()->where(['status' => 'enabled'])->all();
                foreach ($companies as $company){
                    //Stock primario
                    $stock = $model->getStock($company, true);
                    //Stock secundario
                    $secondaryStock = $model->getSecondaryStock($company, true);
                    if($secondaryStock){
                        //Fila
                        $table .= "<tr><td style='padding: 0 10px 0 0;'>$company->name:</td> <td style='padding: 0 3px 0 0; text-align: right; white-space: nowrap;'>$stock</td><td style='padding: 0 0 0 3px; text-align: right; border-left: 1px solid #bbb; white-space: nowrap;'>$secondaryStock</td></tr>";
                    }else{
                        //Fila
                        $table .= "<tr><td style='padding: 0 10px 0 0;'>$company->name:</td> <td style='padding: 0; white-space: nowrap;'>$stock</td></tr>";
                    }
                }
                $table .= '</table>';
                
                return $table;
            }
        ],
        [
            'label' => Yii::t('app', 'Avaible Stock'),
            'visible'=>$searchModel->stock_company_id == null && Yii::$app->params['companies']['enabled'],
            'format'=>'html',
            'value'=>function($model){
                $table = '<table class="table">';
                $companies = app\modules\sale\models\Company::find()->where(['status' => 'enabled'])->all();
                foreach ($companies as $company){
                    //Stock dispoible
                    $avaibleStock = $model->getAvaibleStock($company, true);
                    //Stock secundario disponible
                    $secondaryAvaibleStock = $model->getSecondaryAvaibleStock($company, true);
                    
                    if($secondaryAvaibleStock){
                        //Fila
                        $table .= "<tr><td style='padding: 0 10px 0 0;'>$company->name:</td> <td style='padding: 0 3px 0 0; text-align: right; white-space: nowrap;'>$avaibleStock</td><td style='padding: 0 0 0 3px; text-align: right; border-left: 1px solid #bbb; white-space: nowrap;'>$secondaryAvaibleStock</td></tr>";
                    }else{
                        //Fila
                        $table .= "<tr><td style='padding: 0 10px 0 0;'>$company->name:</td> <td style='padding: 0; white-space: nowrap;'>$avaibleStock</td></tr>";
                    }
                    
                }
                $table .= '</table>';
                
                return $table;
            }
        ],
        //Formato:
        [
            'label'=>$searchModel->getAttributeLabel('netPrice'),
            'attribute'=>'netPrice',
            'format'=>['currency']
        ],
        [
            'label'=>$searchModel->getAttributeLabel('finalPrice'),
            'attribute'=>'finalPrice',
            'format'=>['currency']
        ],
        [
            'class' => 'app\components\grid\ActionColumn',
            'template' => '{price-history}&nbsp;&nbsp;{stock-history}',
            'buttons' => [
                'price-history'=>function($url, $model){
                    return Html::a('<span class="glyphicon glyphicon-tags"></span>', $url, [
                        'title' => Yii::t('yii', 'Price history'),
                        'target'=>'_blank',
                        'data-pjax' => '0',
                    ]);
                },
                'stock-history'=>function($url, $model){
                    return Html::a('<span class="glyphicon glyphicon-stats"></span>', \yii\helpers\Url::toRoute(['stock-movement/index','product_id'=>$model->product_id]), [
                        'title' => Yii::t('yii', 'Stock history'),
                        'target'=>'_blank',
                        'data-pjax' => '0',
                    ]);
                },
            ],
            'header'=>Yii::t('app','History')
        ],
        'description:ntext',
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
            'template' => '{view} {update} {delete} {print-barcodes}',
            'buttons' => [
                'print-barcodes'=>function($url, $model){
                    return Html::a('<span class="glyphicon glyphicon-barcode"></span>', $url, [
                        'title' => Yii::t('yii', 'Print barcodes'),
                        'target'=>'_blank',
                        'data-pjax' => '0',
                    ]);
                }
            ],
            'header'=>Yii::t('app','Operations'),
            'visible'=>!Yii::$app->params['dropdown-operations-list']
        ],
        [
            'class' => '\yii\grid\DataColumn',
            'content' => function($model, $key, $index, $column){
                return yii\bootstrap\ButtonDropdown::widget([
                    'label' => Yii::t('app','Actions'),
                    'dropdown' => [
                        'items' => [
                            ['label' => '<span class="glyphicon glyphicon-eye-open"></span> '.Yii::t('yii', 'View'), 'url' => ['product/view','id'=>$model->product_id]],
                            ['label' => '<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('yii', 'Update'), 'url' => ['product/update','id'=>$model->product_id]],
                            ['label' => '<span class="glyphicon glyphicon-barcode"></span> '.Yii::t('app', 'Print barcodes'), 'url' => ['product/print-barcodes','id'=>$model->product_id], 'linkOptions'=>['target'=>'_blank']],
                            
                            '<li role="presentation" class="divider"></li>',
                            
                            ['label' => '<span class="glyphicon glyphicon-arrow-up"></span> '.Yii::t('app', 'Incoming movement'), 'url' => ['stock-movement/create','product_id'=>$model->product_id,'type'=>'in']],
                            ['label' => '<span class="glyphicon glyphicon-arrow-down"></span> '.Yii::t('app', 'Outgoing movement'), 'url' => ['stock-movement/create','product_id'=>$model->product_id,'type'=>'out']],
                            
                            //Si el modelo es eliminable mostramos el separador y el boton eliminar:
                            $model->deletable ?'<li role="presentation" class="divider"></li>' : '',
                            $model->deletable ? ['label' => '<span class="glyphicon glyphicon-trash"></span> '.Yii::t('yii', 'Delete'), 'url' => ['product/delete','id'=>$model->product_id,'type'=>'out'],'linkOptions'=>['data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),'data-method' => 'post','data-pjax' => '0']] : '',
                        ],
                        'encodeLabels'=>false,
                        'options' => ['class' => 'dropdown-menu dropdown-menu-right']
                    ],
                    'options'=>[
                        'class'=>'btn-sm btn-default'
                    ]
                ]);
            },
            'format'=>'html',
            'visible'=>Yii::$app->params['dropdown-operations-list']
        ]
    ];