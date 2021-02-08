<?php

use app\modules\accounting\assets\JsMovementsPaginateAsset;
use yii\bootstrap\Alert;
use yii\bootstrap\Collapse;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */
JsMovementsPaginateAsset::register($this);
$this->title = Yii::t('accounting', 'Movements') . " - " . $model->moneyBox->name . " - " . $model->number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Money Box Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-movement-index">

    <h1><?= Html::encode($this->title) ?> <small><?= $searchModel->date ?></small></h1>
    
    <?php
    $pending = $model->dailyBoxPendingClose();
    if($pending){
        
        $body = Yii::t('accounting', 'Daily box from {date} has not been closed.', ['date' => $pending]).' ';
        $body .= Html::a('<strong>'.Yii::t('accounting', 'Click here to close.').'</strong>', ['/accounting/money-box-account/close-daily-box', 'id' => $model->money_box_account_id, 'date' => $pending]);
        
        echo Alert::widget([
            'options' => [
                  'class' => 'alert-danger',
            ],
            'body' => $body,
            ]
        );
    }
    ?>
    
    <p>
        <?php 
        if(!$model->isDailyBoxClosed($searchModel->date)){
            
            //Nueva entrada
            if(Yii::$app->formatter->asTimestamp($searchModel->date) > strtotime($model->daily_box_last_closing_date)){
                echo Html::a(
                    '<span class="glyphicon glyphicon-plus"></span> '.Yii::t('accounting', 'Create Entry'), 
                    ['/accounting/account-movement/daily-box-create', 'box_id' => $model->money_box_account_id, 'date' => $searchModel->date],
                    ['class' => 'btn btn-success']
                );
                echo ' ';
            }
            
            //Cierre de caja
            echo Html::a(
                '<span class="glyphicon glyphicon-repeat"></span> '.Yii::t('app', 'Close'), 
                ['/accounting/money-box-account/close-daily-box', 'id' => $model->money_box_account_id, 'date' => $searchModel->date],
                ['class' => 'btn btn-warning',
                    'data' => [
                            'confirm' => Yii::t('accounting', 'Are you sure you want to close this box?'),
                            'method' => 'post',
                ]]
            );
            echo ' ';

            // Exportar
            echo Html::a(
                '<span class="glyphicon glyphicon-export"></span> '.Yii::t('accounting', 'Export'),
                ['/accounting/money-box-account/export', 'id' => $model->money_box_account_id, 'AccountMovementSearch[date]' => $searchModel->date],
                ['class' => 'btn btn-warning']
            );
        }else{
            echo Alert::widget([
                'options' => [
                      'class' => 'alert-info',
                ],
                'body' => Yii::t('accounting', 'This daily box is closed.'),
                ]
            );
        }?>
    </p>
    <?php
    $item = '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters');

    echo Collapse::widget([
        'items' => [
            [
                'label' => $item,
                'content' => $this->render('_daily-box-search', ['model' => $searchModel, 'money_box_account_id' => $model->money_box_account_id]),
                'encode' => false,
            ],
        ]
    ]);

    ?>
    <table class="kv-grid-table table table-bordered table-striped kv-table-wrap" >
        <thead>
            <tr>
                <th><?= Yii::t('app', 'Date')?></th>
                <th><?= Yii::t('app', 'From / To')?></th>
                <th><?= Yii::t('app', 'Description')?></th>
                <th><?= Yii::t('accounting', 'Debit')?></th>
                <th><?= Yii::t('accounting', 'Credit')?></th>
                <th><?= Yii::t('app', 'Balance')?></th>
                <th><?= Yii::t('app', 'Status')?></th>
                <th> </th>
            </tr>
        </thead>
        <tbody id="content">
           
            <?php
                foreach ($data as $key => $movement){
                    echo '<tr id="f'.$key.'">';
                        echo '<td>' . Yii::$app->formatter->asDate($movement['date'], 'dd-MM-yyyy') . '</td>';
                        echo '<td style="'.($movement['debit'] == 0 ? 'text-align: right' : 'text-align: left' ).'">' . ($movement['debit'] != 0 ? $movement['from'] : '  A '. $movement['from'] ). '</td>';
                        echo '<td>' . $movement['description'] . '</td>';
                        echo '<td>' . Yii::$app->formatter->asCurrency($movement['debit']) . '</td>';
                        echo '<td>' . Yii::$app->formatter->asCurrency($movement['credit']) . '</td>';
                        echo '<td>' . Yii::$app->formatter->asCurrency($movement['partial_balance']) . '</td>';
                        echo '<td>' . Yii::t('accounting', ucfirst($movement['status'])) . '</td>';
                        echo '<td>' . '<a href="'. Url::to(['/accounting/account-movement/view', 'id'=> $movement['account_movement_id']]). '" class="btn btn-view"><span class="glyphicon glyphicon-eye-open"></span></a>  '
                                    . ($movement['status'] === 'draft' ? '<a href="'. Url::to(['/accounting/account-movement/close', 'id'=> $movement['account_movement_id'], 'from' => 'daily', 'money_box_account_id' => $movement['money_box_account_id']]). '" class="btn btn-warning"><span class="glyphicon glyphicon-lock"></span></a>' : '').
                              '</td>'; 
                    echo '</tr>';    
                }
            
            ?>
               
        </tbody>
    </table>
    <div class="pagination2">
        
    </div>
    <div class="row">
        <div class="col-sm-6 col-sm-offset-6"><h3><?= Yii::t('accounting', 'Balance of the day') ?></h3></div>
    </div>
    <div class="row">
        <div class="col-sm-4">&nbsp;</div>
        <div class="col-sm-2 text-center">
            <strong><?= Yii::t('accounting', 'Init Balance'); ?></strong>
        </div>
        <div class="col-sm-2 text-center">
            <strong><?= Yii::t('accounting', 'Debit'); ?></strong>
        </div>
        <div class="col-sm-2 text-center">
            <strong><?= Yii::t('accounting', 'Credit'); ?></strong>
        </div>
        <div class="col-sm-2 text-center">
            <strong><?= Yii::t('app', 'Balance'); ?></strong>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">&nbsp;</div>
        <div class="col-sm-2  text-center">
            <?=Yii::$app->formatter->asCurrency($init_balance); ?>
        </div>
        <div class="col-sm-2  text-center">
            <?=Yii::$app->formatter->asCurrency($searchModel->totalDebit); ?>
        </div>
        <div class="col-sm-2 text-center">
            <?=Yii::$app->formatter->asCurrency($searchModel->totalCredit); ?>
        </div>
        <?php
            $balance = $searchModel->totalDebit - $searchModel->totalCredit;
        ?>
        <div class="col-sm-2 text-center <?=($balance < 0 ? 'alert-danger' : '' )?>">
            <?=Yii::$app->formatter->asCurrency( $balance ); ?>
        </div>
    </div>
    
    <?php //Balance total ?>
    <div class="row">
        <div class="col-sm-6 col-sm-offset-6"><h3><?= Yii::t('accounting', 'Total Account') ?></small></h3></div>
    </div>
    <div class="row">
        <div class="col-sm-6">&nbsp;</div>
        <div class="col-sm-2 text-center">
            <strong><?= Yii::t('accounting', 'Debit'); ?></strong>
        </div>
        <div class="col-sm-2 text-center">
            <strong><?= Yii::t('accounting', 'Credit'); ?></strong>
        </div>
        <div class="col-sm-2 text-center">
            <strong><?= Yii::t('app', 'Balance'); ?></strong>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">&nbsp;</div>
        <div class="col-sm-2  text-center">
            <?=Yii::$app->formatter->asCurrency($init_debit_day + $searchModel->totalDebit); ?>
        </div>
        <div class="col-sm-2 text-center">
            <?=Yii::$app->formatter->asCurrency($init_credit_day + $searchModel->totalCredit); ?>
        </div>
        <?php
            $tbalance = $init_balance + $balance;
        ?>
        <div class="col-sm-2 text-center <?=($tbalance < 0 ? 'alert-danger' : '' )?>">
            <?=Yii::$app->formatter->asCurrency( $tbalance ); ?>
        </div>
    </div>

</div>

<script>
    var movementsView = new function(){
        this.init= function(){
            <?= empty($init) ? 'movementsView.set_cookie("current", 1);' : '' ?>
            $('#content').jPaginate({ items: 15}, true);
           $('.pagination a').css('float', 'none');
           $(document).on('click', '.pagination a', function(){
                $('.pagination a').css('float', 'none');
            })
        };
        
        this.set_cookie= function (c_name, value) {
            var expiredays = 999;
            var exdate = new Date();
            exdate.setDate(exdate.getDate() + expiredays);
            document.cookie = c_name
                + "="
                + escape(value)
                + ((expiredays == null) ? "" : ";expires="
                + exdate.toUTCString());
            }
    }
</script>
<?php
    //$this->registerJsFile(Url::to('@web').'/js/jPaginate.js', ['position' => View::POS_END]);
    $this->registerJs('movementsView.init();', View::POS_END);
?>