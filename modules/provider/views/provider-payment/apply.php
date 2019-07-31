<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\Payment */

$this->title = Yii::t('app','Payment').' '.$model->provider_payment_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-view">
    <input type="hidden" value="<?=$model->provider_payment_id?>" name="payment_id" id="payment_id"/>
    <h1><?= Html::encode($this->title) ?></h1>


<?php Pjax::begin(['id'=>'w_header']);?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= $this->title ?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-2 text-center">
                    <strong><?= Yii::t('app', 'Date'); ?></strong>
                    <br/>
                    <?= $model->date ?>
                </div>
                <div class="col-sm-4 text-center">
                    <strong><?= Yii::t('app', 'Description'); ?></strong>
                    <br/>
                    <?= $model->description ?>
                </div>
                <div class="col-sm-2 text-center">
                    <strong><?= Yii::t('app', 'Amount'); ?></strong>
                    <br/>
                    <?= Yii::$app->formatter->asCurrency( $model->amount ) ?>
                </div>
                <div class="col-sm-2 text-center">
                    <strong><?= Yii::t('app', 'Balance'); ?></strong>
                    <br/>
                    <?= Yii::$app->formatter->asCurrency( $model->balance ) ?>
                </div>
            </div>
        </div>
    </div>
<?php Pjax::end();?>
<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <?= Yii::t('app', 'Bills') ?>
                </strong>
                <div class="btn-group btn-group-xs pull-right" role="group">
                    <button type="button" id="btnApply" class="btn btn-warning"><?=Yii::t('app', 'Apply');?></button>
                </div>
            </div>
            <div class="panel-body">
                <?php Pjax::begin(['id'=>'w_bills']);

                echo GridView::widget([
                    'id'=>'grid_bills',
                    'dataProvider' => $billDataProvider,
                    'columns' => [
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'checkboxOptions' => function($model, $key, $index, $column) {
                                return ['value' => $model->provider_bill_id];
                            }
                        ],
                        [
                            'label' => Yii::t('app', 'Bill'),
                            'value' => function($model) {
                                return $model->number;
                            }
                        ],
                        'date',
                        [
                            'attribute' => 'total',
                            'label'     => Yii::t('app', 'Total'),
                            'format'    => ['currency']
                        ],
                        [
                            'attribute' => 'amountApplied',
                            'label'     => Yii::t('app', 'Amount applied'),
                            'format'    => ['currency']
                        ],
                    ],
                    'options'=>[
                        'style'=>'margin-top:10px;'
                    ]
                ]);
                Pjax::end(); ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <?= Yii::t('app', 'Applied bills') ?>
                </strong>
                <div class="btn-group btn-group-xs pull-right" role="group">
                    <button type="button" id="btnDisengage" class="btn btn-warning"><?=Yii::t('app', 'Disengage');?></button>
                </div>
            </div>
            <div class="panel-body">

                <?php Pjax::begin(['id'=>'w_applied_bills']);
                echo GridView::widget([
                    'id'=>'grid_applied_bills',
                    'dataProvider' => $appliedDataProvider,
                    'columns' => [
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'checkboxOptions' => function($model, $key, $index, $column) {
                                return ['value' => $model->provider_bill_id];
                            }
                        ],
                        [
                            'label' => Yii::t('app', 'Bill'),
                            'value' => function($model) {
                                return $model->number;
                            }
                        ],
                        'date',
                        [
                            'attribute' => 'total',
                            'label'     => Yii::t('app', 'Total'),
                            'format'    => ['currency']
                        ],
                        [
                            'attribute' => 'debt',
                            'label'     => Yii::t('app', 'Balance'),
                            'format'    => ['currency']
                        ],
                    ],
                    'options'=>[
                        'style'=>'margin-top:10px;'
                    ]
                ]);
                Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    var PaymentApply = new function(){
        this.init = function() {
            $(document).off("click", "#btnApply" )
                .on("click", "#btnApply", function(){
                PaymentApply.apply();
            });
            $(document).off("click", "#btnDisengage" )
                .on("click", "#btnDisengage", function(){
                    PaymentApply.disengage();
            });

        }

        this.moveBill = function (type) {
            var data = {};
            var url = "";
            if (type=='apply') {
                data.bills = $('#grid_bills').yiiGridView('getSelectedRows');
                url = '<?=Url::toRoute(['asociate-provider-bill', 'id' => $model->provider_payment_id])?>';
            } else {
                data.bills = $('#grid_applied_bills').yiiGridView('getSelectedRows');
                url = '<?=Url::toRoute(['remove-association-with-provider-bill', 'id'=>$model->provider_payment_id])?>';
            }

            if (data.bills.length > 0) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function(data){
                        if (data.status!="success") {
                            console.log(data.errors);
                            alert("<?=Yii::t('app', 'This resource could not be applied.')?>");

                        } else {
                            $.pjax.reload({container: '#w_applied_bills,#w_bills,#w_header'});
                        }
                    }
                });
            }
        }

        this.apply = function() {
            PaymentApply.moveBill('apply');
        }

        this.disengage = function() {
            PaymentApply.moveBill('disengage');
        }
    };
</script>
<?php  $this->registerJs("PaymentApply.init();"); ?>