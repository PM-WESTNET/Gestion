<?php

use app\modules\westnet\models\Node;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
?>

<div class="pendings-instalations-filters">
    <?php $form = ActiveForm::begin(['method' => 'get', 'id' => 'filterForm']) ?>
        <div class="row">
            <div class="col-sm-3">
            <?= $form->field($model, 'document_number')->textInput() ?>
            </div>
            <div class="col-sm-3">
                <?= $this->render('_find-with-autocomplete_multiple', ['model' => $model, 'form' => $form, 'model_attribute' => 'customer_number', 'table_attribute' => 'code']) ?>        
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'name')->textInput() ?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'last_name')->textInput() ?>
            </div>

        </div>
        <div class="row">
            <div class="col-sm-3">
                <?= $form->field($model, 'date')->widget(DatePicker::className(), [
                        'language' => Yii::$app->language,
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control dates',
                            
                        ]
                ]) ?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'tentative_node')->dropDownList(ArrayHelper::map(array_merge([ ['subnet'=>'null', 'name'=> Yii::t('app','Without Tentative Node')]], Node::find()->select(['subnet', 'name'])->all()), 'subnet', 'name'), ['prompt' => Yii::t('app','Select an option...')])?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'vendor_id')->dropDownList(ArrayHelper::map(\app\modules\westnet\models\Vendor::find()->all(), 'vendor_id', 'fullName'), ['prompt' => Yii::t('app','Select an option...') ]) ?>
            </div>
            <div class="col-sm-3">
                <?= $this->render('_find-zone-with-autocomplete', ['form' => $form, 'model'=> $model]) ?>
            </div>
            
            
        </div>        
        <div class="row">
            <div class="col-sm-1 ">
                <?=  Html::submitInput('Filtrar', ['class'=> 'btn btn-primary', 'id'=> 'filterButton'])?>
            </div>
            <div class="col-sm-1">
                <?= Html::a('Borrar Filtros', Url::to(['customer/pending-installations']), ['class' => 'btn btn-default'])?>
            </div>
        </div>


<?php $form->end(); ?>     
</div>

