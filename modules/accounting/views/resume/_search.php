<?php

use app\modules\accounting\models\Account;
use app\modules\accounting\models\Resume;
use app\modules\paycheck\models\Paycheck;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\BillType;
use app\modules\sale\models\Currency;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\search\BillSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="resume-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'id' => 'resume-search-form',
        'action' => ['resume/index']
    ]); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= app\components\companies\CompanySelector::widget(['model' => $model, 'inputOptions' => ['prompt' => Yii::t('app', 'All')]]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'statuses')->checkboxList([
                Resume::STATE_DRAFT => Yii::t('accounting', ucfirst(Resume::STATE_DRAFT)),
                Resume::STATE_CLOSED => Yii::t('accounting', ucfirst(Resume::STATE_CLOSED)),
                Resume::STATE_CONCILED => Yii::t('accounting', ucfirst(Resume::STATE_CONCILED)),
                Resume::STATE_CANCELED => Yii::t('accounting', ucfirst(Resume::STATE_CANCELED)),
            ], ['separator' => '<br>']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'fromDate')->widget(yii\jui\DatePicker::className(), [
                        'language' => 'es',
                        'model' => $model,
                        'attribute' => 'fromDate',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control dates',
                            'id' => 'from-date'
                        ]
                    ]);
                    ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'toDate')->widget(yii\jui\DatePicker::className(), [
                        'language' => 'es',
                        'model' => $model,
                        'attribute' => 'toDate',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class' => 'form-control dates',
                            'id' => 'to-date'
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <?php echo $this->render('@app/modules/accounting/views/money-box-account/_selector', [
            'model' => $model,
            'form' => $form,
            'style' => 'horizontal',
            'money_box_id_name' => 'ResumeSearch[money_box_id]',
            'money_box_id_access' => 'ResumeSearch[money_box_id]',
        ]); ?>
    </div>

    <hr>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Clear'), "#", ['class' => 'btn btn-default', 'id'=> 'btnClear']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    var ResumeSearch = new function(){
        this.init = function () {
            $(document).off("click", "#btnClear")
                .on("click", "#btnClear", function(){
                ResumeSearch.clear();
            });
        }
        this.clear = function () {
            $(".resume-search form input[type='text'],.resume-search form select").val("");
            $(".resume-search form input[type='checkbox']").removeAttr("checked");
        }
    }
</script>
<?php $this->registerJs("ResumeSearch.init();"); ?>