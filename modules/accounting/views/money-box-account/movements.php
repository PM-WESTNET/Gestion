<?php

use app\modules\accounting\assets\JsMovementsPaginateAsset;
use yii\bootstrap\Collapse;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use kartik\export\ExportMenu;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */
JsMovementsPaginateAsset::register($this);
$this->title = Yii::t('accounting', 'Movements') . " - " . $model->moneyBox->name . " - " . $model->number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Money Box Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

    
?>


<div class="account-movement-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php

    if ($fromMoneyBox) {
        echo '<h4>'.Yii::t('accounting', 'The balance is from the Parent Account')."</h4>";
    }
    
    echo Html::a(
                    '<span class="glyphicon glyphicon-plus"></span> '.Yii::t('accounting', 'Create Entry'), 
                    ['/accounting/account-movement/create'],
                    ['class' => 'btn btn-success']
                );
    $item = '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters');

    echo Collapse::widget([
        'items' => [
            [
                'label' => $item,
                'content' => $this->render('_search', ['model' => $searchModel, 'money_box_account_id' => $model->money_box_account_id]),
                'encode' => false,
            ],
        ]
    ]);

    // Renders a export dropdown menu
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'showFooter' => true,
        'columns' => [
            [
                'label' => Yii::t('app', 'Date'),
                'value' => function($model) {
                    return $model['date'];
                }
            ],
            [
                'label' => Yii::t('app', 'From / To'),
                'value' => function($model) {
                    return ($model['debit'] != 0 ? $model['from'] : '  A '. $model['from']);
                }
            ],
            [
                'label' => Yii::t('app', 'Description'),
                'value' => function($model) {
                    return $model['description'];
                }
            ],
            [
                'label' => Yii::t('app', 'Description'),
                'value' => function($model) {
                    return $model['description'];
                }
            ],
            [
                'label' => Yii::t('accounting', 'Debit'),
                'value' => function($model) {
                    return Yii::$app->formatter->asCurrency($model['debit']);
                },
                'footer' => Yii::$app->formatter->asCurrency($searchModel->totalDebit),
            ],
            [
                'label' => Yii::t('accounting', 'Credit'),
                'value' => function($model) {
                    return Yii::$app->formatter->asCurrency($model['credit']);
                },
                'footer' => Yii::$app->formatter->asCurrency($searchModel->totalCredit),
            ],
            [
                'label' => Yii::t('app', 'Balance'),
                'value' => function($model) {
                    return Yii::$app->formatter->asCurrency($model['partial_balance']);
                },
                'footer' => Yii::$app->formatter->asCurrency( $searchModel->totalDebit - $searchModel->totalCredit ),
            ],
            [
                'label' => Yii::t('app', 'Status'),
                'value' => function($model) {
                    return Yii::t('accounting', $model['status']);
                }
            ],
        ],
        'showConfirmAlert'=>false
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
                <th>&nbsp;</th>
                <th><?= Yii::t('app', 'Status')?></th>
                <th> </th>
            </tr>
        </thead>
        <tbody id="content">
           
            <?php
                foreach ($data as $key => $movement){
                    echo '<tr id="f'.$key.'" class="'.($movement['check'] ? 'danger' : '' ).'">';
                        echo '<td>' . Yii::$app->formatter->asDate($movement['date'], 'dd-MM-yyyy') . '</td>';
                        echo '<td style="'.($movement['debit'] == 0 ? 'text-align: right' : 'text-align: left' ).'">' . ($movement['debit'] != 0 ? $movement['from'] : '  A '. $movement['from'] ). '</td>';
                        echo '<td>' . $movement['description'] . '</td>';
                        echo '<td>' . Yii::$app->formatter->asCurrency($movement['debit']) . '</td>';
                        echo '<td>' . Yii::$app->formatter->asCurrency($movement['credit']) . '</td>';
                        echo '<td>' . Yii::$app->formatter->asCurrency($movement['partial_balance']) . '</td>';
                        echo '<td><input type="checkbox" '.($movement['check'] ? 'checked' : '' ).' class="check_movement" data-id="'.$movement['account_movement_id'].'" /> </td>';
                        echo '<td>' . Yii::t('accounting', $movement['status']) . '</td>';
                        echo '<td>' 
                                    . '<a href="'. Url::to(['/accounting/account-movement/view', 'id'=> $movement['account_movement_id']]). '" class="btn btn-view"><span class="glyphicon glyphicon-eye-open"></span></a>  '
                                    . ($movement['status'] === 'draft' ? '<a href="'. Url::to(['/accounting/account-movement/close', 'id'=> $movement['account_movement_id'], 'from' => 'movements', 'money_box_account_id' => $movement['money_box_account_id'], 'from_date' => $searchModel->fromDate, 'to_date' =>$searchModel->toDate ]). '" class="btn btn-warning"><span class="glyphicon glyphicon-lock"></span></a>' : '').
                              '</td>'; 
                    echo '</tr>';    
                }
            
            ?>
               
        </tbody>
    </table>
    <div class="pagination2">
        
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

</div>
        
        
<script>
    var movementsView = new function(){
    this.init= function(){
        <?= empty($init) ? 'movementsView.set_cookie("current", 1);' : '' ?>
        $('#content').jPaginate({ items: 15}, true);
        $('.pagination a').css('float', 'none');

        $(document).on('click', '.pagination a', function(){
            $('.pagination a').css('float', 'none');
        });

        $(document).off('click', 'input.check_movement')
            .on('click', 'input.check_movement', function(){
            var $this = $(this);
            var id = $(this).data('id');
            var checked = ($(this).is(':checked') ? 1 : 0);
            $.post('<?php echo Url::to(['/accounting/account-movement/check' ])?>',{
                    'account_movement_id': id,
                    'checked': checked
            }).done(function(data){
                if(checked) {
                    $this.parent().parent().addClass('danger');
                } else {
                    $this.parent().parent().removeClass('danger');
                }
            });
        });
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
