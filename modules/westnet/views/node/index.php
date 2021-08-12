<?php

use app\modules\westnet\models\search\NodeSearch;
use app\modules\zone\models\Zone;
use yii\bootstrap\Collapse;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel NodeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('westnet','Nodes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="node-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= \app\components\helpers\UserA::a(Yii::t('app', 'Node Change Processes'),  ['node-change-process/index'], ['class' => 'btn btn-warning'])?>
            <?= \app\components\helpers\UserA::a(Yii::t('app', 'Init massive node change process'),  ['node-change-process/create'], ['class' => 'btn btn-warning'])?>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " .  Yii::t('app','Create {modelClass}', ['modelClass'=>Yii::t('westnet', 'Node')]),
            ['create'], 
            ['class' => 'btn btn-success']) 
            ;?>
        </p>
    </div>
    
    <div class="nodes-search">

        <?php

    $item = '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters');



    echo Collapse::widget([
        'items' => [
            [
                'label' => $item,
                'content' => $this->render('_filters-node', ['searchModel' => $searchModel]),
                'encode' => false,
            ],
        ],
        'options' => [
            'class' => 'print',
            'aria-expanded' => 'false'
        ]
    ]);
    ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        
        'columns' => [
            

            'node_id',
            [
                'label'=> Yii::t('westnet', 'Server'),
                'attribute'=> 'server.name',
                'value' => function ($model){
                    return $model->server->name;
                }
            ],
            [
                'label' => Yii::t('westnet', 'Parent Node'),
                'attribute' => 'n2.name',
                'value' => function ($model){
                    if(!empty($model->parentNode)){
                        return $model->parentNode->name;
                    }
                    
                }

            ],
            [
                'label' => Yii::t('app', 'Name'),
                'attribute' => 'node.name',
                'value' => function($model){
                    return $model->name;
                }
            ],
            [
                'label' => Yii::t('app', 'Status'),
                'attribute'=>'node.status',
                'filter'=>[
                    'enabled'=>Yii::t('westnet','Enabled'),
                    'in_progress'=>Yii::t('westnet','In Progress'),
                    'disabled'=>Yii::t('westnet','Disabled'),
                ],
                'value'=>function($model){return Yii::t('westnet',  ucfirst($model->status)); }
            ],
            [
                'label' => Yii::t('app', 'Zone'),
                'attribute'=>'zone.name',
                'filter'=> ArrayHelper::map(Zone::find()->all(), 'zone_id', 'name'),
                'value'=> 'zone.name',
            ],
            [
                'label' => Yii::t('app', 'Subnet'),
                'attribute' => 'node.subnet',
                'value' => function($model){
                    return $model->subnet;
                }
            ],
            [
                'label' => Yii::t('westnet', 'Has Ecopago Close'),
                'attribute' => 'node.has_ecopago_close',
                'value' => function($model){
                    return ($model->has_ecopago_close ? Yii::t('app', 'Yes') : Yii::t('app', 'No')) ;
                }
            ],
            [
                'attribute' => 'vlan',
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>'{view} {update} {delete} {move}',
                'buttons'=>[
                    'move' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-random"></span>', "#", [
                            'title' => Yii::t('westnet', 'Change Server'),
                            'class' => 'btn btn-warning change-server',
                            'data-node-id' => $model->node_id
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

</div>

<div id="change-server-modal" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?=Yii::t('westnet', 'Change Server')?></h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal"><?=Yii::t('app', 'Close')?></button>
                <button type="button" class="btn btn-primary" id="change-server-confirm"><?=Yii::t('westnet', 'Change Server')?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    var NodeList = new function () {
        var self = this;
        var processing = false;

        this.init = function() {
            $(document).off('click', '.change-server').on('click', '.change-server', function(){
                if(confirm('<?php echo Yii::t('westnet', 'You are sure to change the server of this node ?'); ?>')) {
                    self.loadChangeServer($(this).data('node-id'));
                }
            });

            $(document).off('click', '#change-server-confirm').on('click', '#change-server-confirm', function(){
                self.changeServer();
            });

        }

        this.loadChangeServer = function (node_id) {
            $.ajax({
                url: '<?php echo \yii\helpers\Url::toRoute('/westnet/node/load-change-server'); ?>&node_id=' + node_id ,
                success: function(data){
                    $('#change-server-modal .modal-body').html(data);
                    $('#change-server-modal').modal();
                }
            });
        };

        this.changeServer = function() {
            if(!self.processing && $('#server_id').val() ) {
                self.processing = true;

                setTimeout(function(){
                    self.getProceso()
                },1000);
                setTimeout(function(){
                    $('#server_id').prop('disabled', true);
                    $('#change-server-confirm').prop('disabled', true);
                    $('#close').prop('disabled', true);
                    $.ajax({
                        url: '<?php echo \yii\helpers\Url::toRoute('/westnet/node/change-server'); ?>',
                        data: {
                            'node_id': $('#change-server-modal #form-change-server #node_id').val(),
                            'server_id': $('#change-server-modal #form-change-server #server_id').val(),
                        },
                        method: 'POST',
                        success: function(data){
                            self.processing = false;
                            $('#close').removeProp('disabled');
                        }
                    });
                }, 500);
            }
        }


        this.getProceso = function() {
            setTimeout(function(){
                $.ajax({
                    method: 'POST',
                    url: '<?php echo \yii\helpers\Url::to(['/westnet/node/get-process'])?>',
                    data: {
                        'process': '_change_node_'
                    },
                    dataType: 'json',
                    success: function(data, textStatus, jqXhr) {
                        var value = ((data.qty*100)/data.total);
                        $('.progress-bar').css('width', value+'%').attr('aria-valuenow', value);

                        $('#total_to_process').html(data.total);
                        $('#processed').html(data.qty);
                        if(data.total!=data.qty) {
                            $('.progress-bar').html(parseInt(value) +'%');
                        } else {
                            $('.progress-bar').html('<?php echo Yii::t('app', 'Process finished') ?>');
                        }
                        if( self.processing ) {
                            self.getProceso();
                        }
                    }
                });
            }, 1000)
        }
    }
</script>
<?php  $this->registerJs("NodeList.init();"); ?>
