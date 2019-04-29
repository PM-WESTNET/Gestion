<?php

use app\modules\westnet\models\Node;
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\components\companies\CompanySelector;

$this->title = Yii::t('app', 'Create Empty ADS');
$this->params['breadcrumbs'][] = $this->title;
$model = new Node();
?>
    <div class="title">
        <h1><?= $this->title ?></h1>
    </div>
    <p>
        Seleccione un nodo y especifique la cantidad de formularios ADS que desee crear.
    </p>
    <hr/>
<?php $form = ActiveForm::begin(['id'=>'form-ads']) ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-xs-6">
                <?= CompanySelector::widget([
                        'model' => $model,
                        'attribute' => 'company_id',
                        'showCompanies' => 'parent',
                        'inputOptions' => [
                                'prompt' => Yii::t('app', 'All'),
                                'id' => 'company_id'
                        ]]) ?>
            </div>
            <div class="col-sm-6 col-xs-6">
                <?php
                echo $form->field($model, 'node_id')->widget(Select2::classname(), [
                    'language' => 'es',
                    'data' => yii\helpers\ArrayHelper::map(Node::findAll(['status'=>'enabled']), 'node_id', 'name'),
                    'options' => [
                        'multiple' => true,
                        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false, 'id'=>'node_id'],
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label(Yii::t('westnet', 'Node'));
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?php echo $form->field($model, 'count')->textInput()->label(Yii::t('app', 'ADS Count')) ?>
            </div>
        </div>
        <div class="row">
            <?= \yii\bootstrap\Html::submitButton(Yii::t('app', 'Generate Empty ADS'), ['id' => 'generate', 'class' => 'btn btn-success']); ?>
            <?php $form->end(); ?>
        </div>
    </div>
    <script>
        var EmptyAds = new function () {
            this.init = function () {
                $(document).off('click', '#generate').on('click', '#generate', function (e) {
                    e.preventDefault();
                    var cantidad = $("#node-count").val();
                    var node = $("#node-node_id").val();
                    var company = $("#company_id").val();

                    if(!company){
                        alert('<?php echo Yii::t('westnet','The company can\'t be empty.')  ?>');
                        return;
                    }

                    if(!cantidad){
                        alert('<?php echo Yii::t('westnet','The quantity must be greater than 0.')  ?>');
                        return;
                    }


                    if(!node){
                        alert('<?php echo Yii::t('westnet','The node can\'t be empty.')  ?>');
                        return;
                    }
                    var url = "<?= yii\helpers\Url::to(['/westnet/ads/print-empty-ads'])?>" +
                        "&company_id=" + company + "&node_id=" + node +  "&qty=" + cantidad;

                    window.open(url);
                });
            };


        }
    </script>

<?php $this->registerJS('EmptyAds.init();'); ?>