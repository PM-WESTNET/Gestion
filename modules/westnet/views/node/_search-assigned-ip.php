<?php

use app\modules\accounting\models\Account;
use app\modules\westnet\models\Node;
use app\modules\westnet\models\Server;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\BillType;
use app\modules\sale\models\Currency;
use yii\widgets\MaskedInput;
use app\modules\sale\models\CustomerCategory;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\search\BillSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="assigned-ip-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group field-server-id required">
                <?php
                echo $form->field($model, 'server_id')->widget(Select2::className(), [
                        'data' => yii\helpers\ArrayHelper::map(Server::find()->all(), 'server_id', 'name' ),
                        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ]
                );?>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group field-node-id required">
                <?php Html::label(Yii::t('westnet','Node'));
                $query = Node::find();
                $query->select(['node.node_id', 'concat(node.name, \' - \', s.name) as name'])
                    ->leftJoin('server s', 'node.server_id = s.server_id');

                echo $form->field($model, 'node_id')->widget(Select2::className(), [
                        'data' => yii\helpers\ArrayHelper::map($query->all(), 'node_id', 'name' ),
                        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]

                    ]
                );?>
            </div>
        </div>
        
        <div class="col-sm-4">
            <div class="form-group field-customer-class-id required">
                <?php 
                echo $form->field($model, 'customer_class_id')->dropDownList(ArrayHelper::map(CustomerCategory::find()->all(), 'customer_category_id', 'name'), ['prompt' => Yii::t('app', 'Select an option...')])->label(Yii::t('app', 'Customer Category'));?>
            </div>
        </div>
        
    </div>
    <div class="row">

        <div class="col-sm-5">
            <div class="form-group field-ip4 required">
                <?php
                echo Html::label(Yii::t('westnet','IP'));
                echo MaskedInput::widget([
                    'model' => $model,
                    'attribute' => 'ip',
                    'id' => 'ip',
                    'clientOptions' => [
                        'alias' =>  'ip'
                    ],
                ]);
                ?>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <?php
                    echo $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form'=> $form, 'model' => $model, 'attribute' => 'customer_id']);
                ?>
            </div>
        </div>
    </div>
    
    <hr>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning']) ?>
        <?= Html::a(Yii::t('app', 'Clear'), $form->action, ['class' => 'btn btn-info pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>