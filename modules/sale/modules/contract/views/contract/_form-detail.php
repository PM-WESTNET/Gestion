
<?php
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\sale\models\Customer;
use app\modules\sale\models\Product;
use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= Yii::t('app', 'Contract Detail') ?></h3>
    </div>
    <div class="panel-body">

        <?php $form = ActiveForm::begin([
            'id'=>'contractdetail-add-form',
            'action' => ['add-contract-detail', 'id' => $model->contract_id]
        ]); ?>
        <input type="hidden" name="ContractDetail[contract_id]" value="<?=$model->contract_id?>"/>

        <div class="row">
            <div class="col-sm-9 col-md-3">
                <?= $form->field($pbt, 'tax_rate_id')->dropDownList( ArrayHelper::map( Tax::find()->select(["tax_rate.tax_rate_id as tax_id", "CONCAT(tax.name, ' - ', (tax_rate.pct*100), '%') as name"])
                    ->leftJoin("tax_rate", "tax.tax_id = tax_rate.tax_id")->all(), "tax_id", "name"), ['prompt' => 'Select'] )
                ?>
            </div>
            <div class="col-sm-9 col-md-3">
                <?= $form->field($pbt, 'amount')->textInput()  ?>
            </div>
            <div class="col-sm-9 col-md-2">
                <label style="display: block">&nbsp;</label>
                    <div class="btn btn-primary" id="tax-add">
                    <span class="glyphicon glyphicon-plus"></span> <?= Yii::t('app', 'Add') ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>


        <?php \yii\widgets\Pjax::begin();?>
        <?=GridView::widget([
            'id'=>'grid',
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'label' => Yii::t("app", "Tax"),
                    'value' => function($model){
                        return $model->taxRate->tax->name . " " . $model->taxRate->name;
                    }
                ],
                'amount:currency',
                [
                    'class' => 'app\components\grid\ActionColumn',
                    'template'=>'{delete}',
                    'buttons'=>[
                        'delete'=>function ($url, $model, $key) {
                            return '<a href="'.Url::toRoute(['provider-bill/delete-tax', 'provider_bill_id'=>$model->provider_bill_id, 'tax_rate_id'=>$model->tax_rate_id]).
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
        <div class="row">
            <div class="col-sm-9 col-md-3">
                &nbsp;
            </div>
            <div class="col-sm-9 col-md-3">
                <label><?=Yii::t("app", "Total of Invoice")?></label>
            </div>
            <div class="col-sm-9 col-md-2">
                <label><?= Yii::$app->formatter->asCurrency($model->calculateTotal())?></label>
            </div>
        </div>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>