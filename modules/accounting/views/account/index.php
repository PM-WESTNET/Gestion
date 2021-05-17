<?php

use app\modules\accounting\assets\JsTreeAsset;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

JsTreeAsset::register($this);
$this->title = Yii::t('accounting', 'Account Plan');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="account-index col-lg-6">

        <div class="title">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <a id="create" class='btn btn-success'><?= "<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('app','Account')]) ?></a>
            </p>
        </div>

        <div></div>
        <div>
            <?=Html::label(Yii::t("app", "Search"), "search_tree") ?>
            <?=Html::input("text", "search_tree", "", ["id"=>"search_tree"]) ?>
        </div>
        <div id="jstree_accounts"></div>
    </div>
    <div id="account-form" class="account-index col-lg-6">
        <iframe id="account-iframe" style="width: 100%; height: 400px; border: 0;" border="0" >
        </iframe>
    </div>

</div>
<script>
    var Account = new function(){
        this.id = 0;
        this.to = 0;
        this.loadTree = false;

        this.init = function(){

            $('#jstree_accounts').jstree({
                'plugins' : ["dnd", "search"],
                'core' : {
                    'check_callback': true,
                    'data': {
                        'url': '<?=Url::toRoute(['/accounting/account/listtreeaccounts'])?>',
                        'dataType': 'json',
                        'cache': false
                    }
                }
            }).bind("select_node.jstree", function (node, ref_node) {
                Account.show( ref_node.node.id );
            });

            var toClear = false;
            $(document).on("keyup", '#search_tree', function(){
                if(toClear) {
                    clearTimeout(toClear);
                }
                toClear = setTimeout(function () {
                    $('#jstree_accounts').jstree(true).search($('#search_tree').val());
                }, 250);
            });

            // Se captura el evento de drag y guardamos el id del nodo
            $(document).on('dnd_start.vakata', function (e, data) {
                if(data.data.jstree && data.data.origin) {
                    Account.id = data.data.origin.get_node(data.element).id;
                }
            });

            // Se captura el evento de drop y ahi disparamos el evento
            $(document).on('dnd_stop.vakata', function (e,data) {
                if(data.data.jstree && data.data.origin) {
                    Account.to = data.data.origin.get_node(data.event.target).id;
                    Account.move();
                }
            });

            // Se captura el evento de click para crear una nueva cuenta
            $(document).on('click', "#create", function () {
                Account.create($("#jstree_accounts").jstree('get_selected')[0]);
            });

            $('#account-iframe').off('load').on('load', function(){
                if(Account.loadTree) {
                    $('#jstree_accounts').jstree(true).refresh();
                    Account.loadTree = false;
                }
            });

        };

        // Actualizo el padre del nodo movido
        this.move = function() {
            if ( Account.id != 0 && Account.to >= 0 ) {
                $.ajax({
                    url: 'index.php?r=accounting/account/moveaccount',
                    data: {
                        id:  Account.id,
                        to:  Account.to
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(data){
                        if (data.status!="success") {
                            //TODO reveritr movimiento
                            alert("<?=Yii::t('accounting', 'This resource could not be moved.')?>");
                        }
                    }
                });
            }
        }

        this.create = function(parent) {
            $("#account-iframe").attr("src", '<?=Url::toRoute(['/accounting/account/create?parent_account_id=' ])?>'+parent);
        };

        this.show = function(id) {
            if(id != 0) {
                $("#account-iframe").attr("src",'<?=Url::toRoute(['/accounting/account/view?id=' ])?>'+id );
            }
        };

        this.save = function(){
            var form = $($($('#account-iframe').contents()[0]).find("form")).get(0)
            Account.loadTree = true;
            form.submit();

        };

        this.delete = function(elem){
            if(confirm("<?=Yii::t('app', 'Are you sure you want to delete this item?')?>")) {
                var id = $(elem).data("id");
                $.ajax({
                    url: '<?=Url::toRoute(['/accounting/account/delete?id=' ])?>'+id,
                    method: 'POST',
                    dataType: 'json',
                    success: function(data){
                        if (data.status!="success") {
                            alert("<?=Yii::t('app', 'This resource could not be deleted.')?>");

                        } else {
                            $($($('#account-iframe').contents()[0])[0]).find("body").html("");
                        }
                        $('#jstree_accounts').jstree(true).refresh();
                    }
                });
            }
        }

    };
</script>
<?php  $this->registerJs("Account.init();"); ?>