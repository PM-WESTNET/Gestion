<?php

use app\modules\westnet\models\Server;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('westnet', 'Restore Customer of Server');
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Servers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="server-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group field-server-id required">
                <label><?php echo Yii::t('westnet', 'Origin Server') ?></label>
                <?php
                echo Html::hiddenInput('server_id', $model->server_id).""
                ?>
                <div class="form-control">
                    <?php echo $model->name ?>
                </div>

            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-3">
            <?=Html::label(Yii::t('westnet', "Total of Customer to Move"))?>
        </div>
        <div class="col-sm-1">
            <?php echo $qty ?>
        </div>
        <div class="col-sm-3 on-move" style="display:none">
            <?=Html::label(Yii::t('westnet', "Restored Successfully"))?>
        </div>
        <div class="col-sm-1 on-move" id="success" style="display:none"></div>
        <div class="col-sm-3 on-move" style="display:none">
            <?=Html::label(Yii::t('westnet', "With Error"))?>
        </div>
        <div class="col-sm-1 on-move" id="error" style="display:none"></div>
    </div>
    <hr>
    <div class="form-group">
        <button type="button" id="btnMover" class="btn btn-success"><?=Yii::t('westnet', 'Restore');?></button>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<script>
    var MoveCustomer = new function() {
        this.init = function () {
            $(document).off('click', '#btnMover').on('click', '#btnMover', function () {
                MoveCustomer.move();
            });
            $('.on-move').hide();

        }

        this.move = function () {
            if(confirm('<?php echo Yii::t('westnet', 'This process can take a few minutes, Are you sure?') ?>') ) {
                $.ajax({
                    url: "<?=Url::toRoute(['/westnet/server/restore-customers', 'id' => $model->server_id])?>",
                    method: 'POST',
                    dataType: 'json',
                    success: function (data) {
                        var errors = data.errors.length;
                        var qty = data.qty;
                        $('#error').html(errors);
                        $('#success').html(qty-errors);

                        $('.on-move').show();
                    }
                });
            }

        }
    }
</script>
<?php  $this->registerJs("MoveCustomer.init();"); ?>