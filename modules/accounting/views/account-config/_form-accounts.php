
<?php

use app\modules\accounting\models\Account;
use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountConfig */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= Yii::t('accounting', 'Assigned Accounts') ?></h3>
    </div>
    <div class="panel-body">

        <?php $form = ActiveForm::begin([
            'id'=>'account-add-form',
            'action' => ['add-account', 'id' => $model->account_config_id]
        ]); ?>
        <input type="hidden" name="AccountConfigHasAccount[account_config_id]" value="<?=$model->account_config_id?>"/>

        <div class="row">
            <div class="col-sm-9 col-md-4">
                <?= $form->field($aca, 'account_id')->widget(Select2::className(),[
                    'data' => yii\helpers\ArrayHelper::map(Account::getForSelect(), 'account_id', 'name' ),
                    'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]);
                ?>
            </div>
            <div class="col-sm-9 col-md-3">
                <?= $form->field($aca, 'is_debit')->dropDownList( ['1'=>Yii::t("accounting", "Debit"), '0'=>Yii::t("accounting", "Credit")], ['prompt' => 'Select'] )  ?>
            </div>
            <div class="col-sm-9 col-md-3">
                <?= $form->field($aca, 'attrib')->dropDownList( $model->getModelAttribs(), ['prompt' => 'Select'] )  ?>
            </div>
            <div class="col-sm-9 col-md-2">
                <label style="display: block">&nbsp;</label>
                <div class="btn btn-primary" id="account-add">
                    <span class="glyphicon glyphicon-plus"></span> <?= Yii::t('app', 'Add') ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>


        <?php \yii\widgets\Pjax::begin();?>
        <?php
        $attribs = $model->getModelAttribs();
        echo GridView::widget([
            'id'=>'grid',
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'table-responsive'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'account.name',
                [
                    'label'=>Yii::t('accounting', 'Debit/Credit'),
                    'value'=> function($model, $key){
                        return ($model->is_debit ? Yii::t('accounting', 'Debit') : Yii::t('accounting', 'Credit') );
                    }
                ],
                [
                    'label' => Yii::t('accounting', 'Attribute'),
                    'value' => function($model, $key) use ($attribs){
                        return $attribs[$model->attrib];
                    }
                ],
                [
                    'class' => 'app\components\grid\ActionColumn',
                    'template'=>'{delete}',
                    'buttons'=>[
                        'delete'=>function ($url, $model, $key) {
                            return '<a href="'.Url::toRoute(['account-config/delete-account', 'account_config_id'=>$model->account_config_id, 'account_id'=>$model->account_id]).
                            '" title="'.Yii::t('app','Delete').'" data-confirm="'.Yii::t('yii','Are you sure you want to delete this item?').'" data-method="post" data-pjax="0"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                    ]
                ],
            ],
            'options'=>[
                'style'=>'margin-top:10px;'
            ]
        ]);
        ?>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>