<?php

use app\modules\westnet\models\Node;
use app\modules\westnet\models\Server;
use app\modules\zone\models\Zone;
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php $form= ActiveForm::begin(['method' => 'GET']);?>
<div class="filters_node">

    <div class="row">
        <?= app\components\companies\CompanySelector::widget(['model' => $searchModel, 'inputOptions' => ['prompt' => Yii::t('app', 'All')]]) ?>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <?= $form->field($searchModel, 'name')->textInput()?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($searchModel, 'server_id')->widget(Select2::className(), [
                'data' => ArrayHelper::map(Server::find()->all(), 'server_id', 'name'),
                'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($searchModel, 'status')->checkboxList(['enabled'=>'Habilitado', 'disabled'=>'Deshabilitado'])?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($searchModel, 'zone_id')->widget(Select2::className(), [
                'data' => ArrayHelper::map(Zone::getForSelect(), 'zone_id', 'name'),
                'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($searchModel, 'parent_node_id')->widget(Select2::className(), [
                'data' => ArrayHelper::map(Node::find()->all(), 'node_id', 'name'),
                'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($searchModel, 'subnet')->textInput()?>
        </div>
    </div>  
    
     <div class="row">
        <div class="col-sm-1 ">
            <?=  Html::submitInput('Filtrar', ['class'=> 'btn btn-primary'])?>
        </div>
        <div class="col-sm-1">
            <?= Html::a('Borrar Filtros', Url::to(['node/index']), ['class' => 'btn btn-default'])?>
        </div>
    </div>
</div>  
<?php $form->end(); ?>

