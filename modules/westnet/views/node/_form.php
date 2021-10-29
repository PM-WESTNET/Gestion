<?php

use app\modules\westnet\models\search\NodeSearch;
use app\modules\westnet\models\Server;
use kartik\widgets\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\Slider;
use yii\widgets\ActiveForm;
use app\modules\zone\models\Zone;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\Company;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\Node */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="node-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'server_id')->label(Yii::t('westnet', 'Server'))
        ->dropDownList( ArrayHelper::map(Server::find()->andWhere(['status'=>'enabled'])->all(), 'server_id', 'name' ), [
            'prompt' => Yii::t('app','Select'),
            'id' => 'server_id'
        ]) ?>

    <?php
    $search = new NodeSearch();
    $data = [];
    if(isset($model->node_id)) {
        $data = ArrayHelper::map( $search->searchPossibleParentNodes($model->server_id, $model->node_id), 'node_id', 'name');
    }
    
    echo $form->field($model, 'parent_node_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'parent_node_id', 'prompt' => Yii::t('westnet', 'Without Parent Node'),],
        'data' => $data,
        'pluginOptions' => [
            'depends' => ['server_id'],
            'initDepends' => 'server_id',
            'placeholder' => Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('westnet','Parent Node')]),            
            'url' => Url::to(['/westnet/node/parent-nodes']),
            'params' => ['node_id']
        ]
    ])->label(Yii::t('westnet', 'Parent Node'));
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'status')->dropDownList(['enabled'=>Yii::t('app','Enabled'),'in_progress'=>Yii::t('westnet','In Progress'), 'disabled'=>Yii::t('app','Disabled')]) ?>

    <?= $form->field($model, 'has_ecopago_close')->checkbox() ?>

    <?php echo $form->field($model, 'subnet', ['template' => '<div class="form-group">
                {label}{input}
                <span class="help-block">'. Yii::t('app', 'If node has access point, subnet must be empty').'</span>
    </div>'])->textInput();?>

    <?php echo $form->field($model, 'zone_id')->widget(Select2::class,[
            'data' => ArrayHelper::map(Zone::getForSelect(), 'zone_id', 'name' ),
            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]);?>
    </br>
    <?php echo $form->field($model, 'nat_server_id')->widget(Select2::class,[
            'data' => $list_nat_servers,
            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label('Nat Server');?>

    <?= $form->field($model, 'vlan')->textInput(['maxlength' => 4]) ?>
    <?= $form->field($model, 'smartolt_olt_id')->textInput(['maxlength' => 6]) ?>

    <?= $form->field($model, 'ecopagos')->checkboxList(yii\helpers\ArrayHelper::map(app\modules\westnet\ecopagos\models\Ecopago::find()->all(),'ecopago_id','name'),['encode'=>false, 'separator'=>'<br/>'])?>
    
    <?= $this->render('_address', ['model' => $model, 'form' => $form])?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
