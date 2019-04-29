<?php

use app\modules\sale\models\Product;
use app\modules\sale\models\search\FundingPlanSearch;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\models\Vendor;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use webvimark\modules\UserManagement\models\User;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Contract */
/* @var $form ActiveForm */
?>

<!-- Carga de adicionales -->

    <div class="col-md-5">
        <div id="adicionales" class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::t('app','Additionals'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <?php $form = ActiveForm::begin([
                        'id' => 'form-additional'
                    ]); ?>
                    
                    <input type="hidden" name="is_customer_detail" id="is_customer_detail" value="0"/>
                    
                    <?=Html::hiddenInput('ContractDetail[contract_detail_id]', $model->contract_detail_id, ['id'=>'contract_detail_id']);?>
                    <?=Html::hiddenInput('ContractDetail[contract_id]', $model->contract_id, ['id'=>'contract_id']);?>

                    <div id="message"></div>
                    <?php if(User::hasPermission('user-can-select-vendor')): ?>
                    <div class="col-md-12">
                        <?php
                        $vendors = Vendor::find()->orderBy(['lastname' => SORT_ASC, 'name' => SORT_ASC])->all();

                        $select = [];
                        foreach($vendors as $vendor){
                            $select[$vendor->vendor_id] = "$vendor->lastname, $vendor->name";
                        }

                        echo $form->field($model, 'vendor_id')->dropDownList($select, ['prompt' => '']) ?>
                    </div>
                    <?php endif; ?>
                    

                    <div class="col-md-12">
                            <?=$form->field($model, 'count')->textInput(['id' => 'count'])?>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php
                            echo Html::label(Yii::t('app', "Product"), ['product_id']);
                            if (User::hasRole('seller', false) && !User::hasRole('seller-office')) {
                                $query= Product::find()
                                        ->joinWith('categories')      
                                        ->where(['product.status'=>'enabled'])  
                                        ->andWhere(['category.system' => 'seller-product'])
                                        ->andWhere(['or',
                                            ['company_id'=>$customer->parent_company_id],
                                            ['company_id'=>null]
                                        ])
                                        ->andFilterWhere(['<>', 'type','plan'])
                                        ->all();
                                $products= ArrayHelper::map($query, 'product_id', 'name' );
                                $options= ArrayHelper::map($query, 'product_id', 'type');
                            }else{
                                $query = Product::find()
                                           ->where(['status'=>'enabled'])  
                                           ->andFilterWhere(['<>', 'type','plan'])
                                            ->andWhere(['or',
                                                ['company_id'=>$customer->parent_company_id],
                                                ['company_id'=>null]
                                            ])
                                           ->all();
                                $products= ArrayHelper::map($query, 'product_id', 'name' );
                                $options= ArrayHelper::map($query, 'product_id', 'type');
                            }
                            //$options = yii\helpers\ArrayHelper::map(Product::find()->where(['status'=>'enabled'])->andFilterWhere(['<>', 'type','plan'])->all(), 'product_id', 'type' );
                           
                            $optionVals = [];
                            foreach($options as $key=>$value){
                                $optionVals[$key] = ['data-type'=>$value];
                            }

                             echo Select2::widget([
                                'model' => $model,
                                'attribute' => 'product_id',
                                'name' => 'product_id',
                                'data' => $products,
                                'options' => [
                                    'placeholder' => Yii::t("app", "Select"),
                                    'encode' => false,
                                    'id' => 'product_id',
                                    'options' => $optionVals
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
                     
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php
                            if ($model->isNewRecord) {
                                $data = [];
                            } else {
                                $search = new FundingPlanSearch();
                                $data =  ArrayHelper::map($search->searchByProduct($model->product_id, 1), 'id', 'name' );
                            }
                            echo $form->field($model, 'funding_plan_id')->widget(DepDrop::classname(), [
                                'options'=>['id'=>'funding_plan_id'],
                                'data'=> $data,
                                'type'=>DepDrop::TYPE_SELECT2,
                                'pluginOptions'=>[
                                    'depends' => ['product_id', 'count'],
                                    'initDepends' => ['product_id', 'count'],
                                    'initialize' => true,
                                    'placeholder' => Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Funding Plan')]),
                                    'url' => Url::to(['/sale/contract/contract/funding-plans'])
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <?php
                        if ($model->isNewRecord) {
                            $data = [];
                        } else {
                            if($model->discount) {
                                $data = [$model->discount->discount_id=>$model->discount->name];
                            } else {
                                $data = [];
                            }
                        }
                        echo $form->field($model, 'tmp_discount_id')->widget(DepDrop::className(), [
                            'options' => ['id' => 'tmp_discount_id'],
                            'data' => $data,
                            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                            'pluginOptions' => [
                                'loading' => true,
                                'depends' => ['product_id', 'is_customer_detail'],
                                'initDepends' => ['product_id', 'is_customer_detail'],
                                'initialize' => true,
                                'placeholder' =>  Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Discount')]),
                                'url' => Url::to(['/sale/discount/discount-by-product'])
                            ]
                        ])->label(Yii::t('app', 'Discount'));
                        ?>
                    </div>
                   
                        <div class="col-md-6" id="divDateFrom">
                            <?=$form->field($model, 'from_date')->widget(DatePicker::classname(), [
                                'type' => 1,
                                'language' => Yii::$app->language,
                                'model' => $model,
                                'attribute' => 'from_date',
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'dd-m-yyyy',
                                    'startDate' => (new DateTime('first day of next month'))->format('d-m-Y'),
                                    'minDate' => (new DateTime('first day of next month'))->format('d-m-Y'),
                                ],
                                'options'=>[
                                    'class'=>'form-control filter dates',
                                    'placeholder'=>Yii::t('app','Date')
                                ]
                            ]);
                            ?>
                        </div>
                        <div class="col-md-6" id="divDateTo">
                            <?=$form->field($model, 'to_date')->widget(DatePicker::classname(), [
                                'type' => 1,
                                'language' => Yii::$app->language,
                                'model' => $model,
                                'attribute' => 'to_date',
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'dd-m-yyyy',
                                ],
                                'options'=>[
                                    'class'=>'form-control filter dates',
                                    'placeholder'=>Yii::t('app','Date')
                                ]
                            ]);
                            ?>
                        </div>
                        
                    <?php //} ?>
                    <div class="col-md-12 col-md-push-9">
                        <?php if ($model->isNewRecord) { ?>
                            <button class="btn btn-success" id="agregar" onclick="return false;"><?= Yii::t('app', 'Add') ?></button>
                        <?php } else { ?>
                            <button class="btn btn-primary" id="agregar" onclick="return false;"><?= Yii::t('app', 'Update') ?></button>
                        <?php } ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div id="adicionales" class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::t('app','Adicionales seleccionados'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <?php  echo GridView::widget([
                                'id'=>'grid',
                                'dataProvider' => $dataProvider,
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                    'count',
                                    'product.name',
                                    [
                                        'header' => Yii::t('app', 'Funding Plan'),
                                        'value' => function($model) {
                                            return ($model->funding_plan_id ?
                                                $model->fundingPlan->getFullName() :
                                                '1 ' . Yii::t('app', 'payment of') . ' '. Yii::$app->formatter->asCurrency($model->product->getFinalPrice()) );
                                        },
                                    ],
                                    [
                                        'header' => Yii::t('app', 'Status'),
                                        'value' => function($model) {
                                            return Yii::t('app', ucfirst($model->status) );
                                        },
                                    ],
                                    [
                                        'class' => 'app\components\grid\ActionColumn',
                                        'template'=>'{update} {delete}',
                                        'buttons'=>[
                                            'delete' => function ($url, $model, $key) {
                                                if($model->getDeletable()){
                                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', null, [
                                                        'title' => Yii::t('yii', 'Delete'),
                                                        'data-confirms' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                                        'class' => 'remove-additional btn btn-danger',
                                                        'data-id' => $key
                                                    ]);
                                                }else{    
                                                   // Si no puedo borrarlo permito cancelarlo
                                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', null, [
                                                        'title' => Yii::t('yii', 'Delete'),
                                                        'data-confirms' => Yii::t('app', 'Are you sure you want to cancel this item?'),
                                                        'class' => 'change-status-additional btn btn-danger',
                                                        'data-id' => $key,
                                                        'data-to-status' => 'cancel'
                                                    ]);
                                                }                                                 
                                            },
                                            'update' => function ($url, $model, $key) {
                                                if ($model->status==Contract::STATUS_DRAFT || (($model->product->hasCategory('instalacion-empresa') || $model->product->hasCategory('instalacion-residencial')) && $model->status==Contract::STATUS_ACTIVE)) {
                                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', null, [
                                                        'title' => Yii::t('yii', 'Update'),
                                                        'class' => 'update-additional btn btn-primary',
                                                        'data-id' => $key,
                                                    ]);
                                                }
                                            }
                                        ]
                                    ]
                                ],
                                'options'=>[
                                    'style'=>'margin-top:10px;'
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var AdditionalForm = new function() {
        this.init = function() {
            $('#tmp_discount_id').on('depdrop.afterChange', function(event, id, value) {
                if($(this).find('option').length == 1) {
                    $($('#tmp_discount_id').find('option[value=""]')[0]).html('<?php echo Yii::t('app', 'No discounts are available')?>')
                }
            });
        }
    }
</script>
<?php $this->registerJs('AdditionalForm.init();');?>
