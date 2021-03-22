<?php

use lavrentiev\widgets\toastr\ToastrAsset;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
use yii\web\Request;

ToastrAsset::register($this);
$this->title = Yii::t('app', 'Assign IP Range');
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/westnet/access-point/view', 'id' => $model->access_point_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Assign IP Range');

?>

<div class="assign-range">

    <h1><?= $this->title?></h1>

    <div class="msg"></div>

    <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => [ 'id' => 'range_table'],
            'columns' => [
                ['class' => CheckboxColumn::class],

                [
                    'attribute' => 'ip_start',
                    'value' => function($model) {
                        return long2ip($model->ip_start);
                    }
                ],
                [
                    'attribute' => 'ip_end',
                    'value' => function($model) {
                        return long2ip($model->ip_end);
                    }
                ],
                
                
            ]
        ])
    
    ?>

    <div class="row">
        <div class="col-lg-12">
            <?= Html::button(Yii::t('app', 'Save'), ['class' => 'btn btn-success', 'id' => 'save-btn'])?>
        </div>
    </div>

    <form id="range-form" method="POST">
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken?>">
    </form>
</div>

<script>

    var AssignRange = new function() {

        this.init = function() {
            $(document).on('click', '#save-btn', function(e) {
                e.preventDefault();
                AssignRange.selectRange();
            })
        }

        this.selectRange = function() {
            var rows = $('#range_table').yiiGridView('getSelectedRows');

            if (rows.length == 0) {
                toastr.error("<?php echo Yii::t('app', 'You must select an Ip Range')?>")
                return;
            }

            if (rows.length > 1) {
                toastr.error("<?php echo Yii::t('app', 'You must select only one Ip Range')?>")
                return;
            }

            rows.forEach(function(row) {
                $('#range-form').append('<input type="hidden" name="AccessPoint[ranges][]" value="'+ row +'">')
            });
            
            $('#range-form').submit();

        }
    }

</script>
<?php $this->registerJs('AssignRange.init()')?>