<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('partner', 'Partner Liquidation');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php if (count($accounts)== 0) { ?>
    <div class="row">
        <div class="col-sm-12">
            <h3><?php echo Yii::t('partner', 'No movements to liquidate.' ) ?></h3>
        </div>
    </div>

    <?php } else {



    ?>
    <?php
        $liquidate = false;
        $company = null;
        foreach( $accounts as $key=>$account) {
            if(is_null($company) || $company != $account['company']){
                if(!is_null($company)){
    ?>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <div class="row">
                    <div class="panel panel-primary" id="panel_operation_type">
                        <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-items" aria-expanded="true" aria-controls="panel-body-items">
                            <h3 class="panel-title"><?php echo $account['company'] ?></h3>
                        </div>
                        <div class="panel-body collapse in" id="panel-body-items" aria-expanded="true">
                            <div class="row">
                                <div class="col-sm-3 text-center"><?php echo Yii::t('app', 'Status')?></div>
                                <div class="col-sm-2 text-center"><?php echo Yii::t('partner', 'Partner')?></div>
                                <div class="col-sm-1 text-center"><?php echo Yii::t('partner', 'Percentage')?></div>
                                <div class="col-sm-2 text-center"><?php echo Yii::t('app', 'Debit')?></div>
                                <div class="col-sm-2 text-center"><?php echo Yii::t('app', 'Credit')?></div>
                                <div class="col-sm-2 text-center"><?php echo Yii::t('app', 'Balance')?></div>
                            </div>
            <?php } ?>
                    <div class="row">
                        <div class="col-sm-3 text-left"><?php echo Yii::t('partner',  'Pending to Liquidate' )  ?></div>
                        <div class="col-sm-2"><?php echo $account['partner'] ?></div>
                        <div class="col-sm-1 text-center"><?php echo Yii::$app->formatter->asPercent($account['percentage']/100) ?></div>
                        <div class="col-sm-2 text-right"><?php echo Yii::$app->formatter->asCurrency($account['debit']) ?></div>
                        <div class="col-sm-2 text-right"><?php echo Yii::$app->formatter->asCurrency($account['credit']) ?></div>
                        <div class="col-sm-2 text-right"><?php echo Yii::$app->formatter->asCurrency($account['credit'] - $account['debit']) ?></div>
                    </div>
    <?php
            $company = $account['company'];
        }
    ?>
        </div>
    </div>
</div>
<?php if (count($account)) { ?>


<div class="row">
    <div class="col-md-12 text-center">
        <a href="#" class="btn btn-default" id="btnLiquidate" data-loading-text="<?php echo Yii::t('app', 'Processing') ?>">
            <span class="glyphicon glyphicon-transfer"></span>&nbsp;<?php echo Yii::t('partner','Liquidate') ?>
        </a>
    </div>
</div>
<?php } ?>
<script>
    var PartnerLiquidation = new function(){
        this.init = function(){
            $(document).off('click', '#btnLiquidate')
                .on('click', '#btnLiquidate', function(){
                    PartnerLiquidation.liquidate(this);
                });
        }
        this.liquidate = function(button){
            var partner_id = $(button).data('id');

            $('#btnLiquidate').button('loading');
            $.ajax({
                method: 'POST',
                url: '<?=Url::to(['/partner/liquidation/liquidate'])?>',
                dataType: 'html',
                data: {
                    'partner_id': partner_id
                },
                success: function(data) {
                    window.location.reload();
                }
            });
        }
    }
</script>
<?php  $this->registerJs("PartnerLiquidation.init();"); ?>
<?php } ?>
