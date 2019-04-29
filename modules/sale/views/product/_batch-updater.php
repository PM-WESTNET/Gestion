<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="panel">
    <div class="panel-body">
        <div class="row">
            <div class="row">
                <div class="col-xs-12" id="batchAlert"></div>
            </div>
            <div class="col-md-6">
                <?php $form = ActiveForm::begin(['id'=>'updaterForm']); ?>

                    <?= $form->field($model, 'percentage') ?>

                    <?= $form->field($model, 'filter')->dropDownList([
                        'all'=>Yii::t('app','All'),
                        'selected'=>Yii::t('app','Selected'),
                        'category'=>Yii::t('app','By category'),
                    ], ['id'=>'batchFilter']) ?>
                
                    <?= $form->field($model, 'category', ['options'=>['style'=>'display:none;']])->dropDownList(
                        \yii\helpers\ArrayHelper::map(\app\modules\sale\models\Category::find()->all(),'category_id','name'), 
                        ['id'=>'batchCategories']) ?>

                    <?php 
                    echo Html::activeLabel($model, 'exp_date');
                    echo yii\jui\DatePicker::widget([
                        'language' => Yii::$app->language,
                        'model' => $model,
                        'attribute' => 'exp_date',
                        'clientOptions' => [
                            'dateFormat' => 'yy-mm-dd',
                        ],
                        'options'=>[
                            'class'=>'form-control'
                        ]
                    ]);
                    ?>
                    <br/>

                    <div class="form-group">
                        <a href="#" onclick="Updater.update()" class="btn btn-primary"><?= Yii::t('app','Update'); ?></a>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-md-6">
                <p>
                    <?= Yii::t('app','This tool allows you to update multiple product prices at the same time. '
                        . 'To do this, you first must choose the percentage that you want to apply to the prices. '
                        . 'Then you can choose a filter to select which products should be updated.') ?>
                </p>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    
    var Updater = new function(){
        
        //private
        function getItems(){
            var keys = $('#grid').yiiGridView('getSelectedRows');
            return keys;
        }
        
        //public
        this.update = function(){
            
            var data = $('#updaterForm').serializeArray();
            data.push({
                name: '<?php $refletion = new \ReflectionClass($model); echo $refletion->getShortName(); ?>[items]',
                value: getItems()
            });
            
            console.dir(data);
            
            $.ajax({
                url: $('#updaterForm').attr('action'),
                data: data,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
                    $('.product-index').css('opacity', '0.5');
                }
            }).done(function(r){
                
                if(r.status == 'success'){
                    
                    location.reload();
                    
                }else if(r.status == 'error'){
                    
                    alert('Error');
                    
                }
                
            }).always(function(){
                $('.product-index').css('opacity', '1');
            });
            
        }
        
    }
    
    $(function(){
        $('#grid input[type="checkbox"]').on('click',function(){$('#batchFilter').val('selected');})
    })
    
    $(function(){
        $('#batchFilter').on('change',function(e){ if($('#batchFilter').val() == 'category') $('.field-updatepriceformmodel-category').show(200); else $('.field-updatepriceformmodel-category').hide(200);})
    })
    
</script>