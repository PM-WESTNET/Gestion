    <?php

use app\modules\accounting\models\Account;
use kartik\widgets\Select2;
use yii\helpers\Html;
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

<div class="bill-search">

    <?php $form = ActiveForm::begin([
        //'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'fromDate')->widget(yii\jui\DatePicker::className(), [
                        'language' => 'es',
                        'model' => $model,
                        'attribute' => 'date',
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
                        'attribute' => 'date',
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
    
    <hr>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary', 'id'=> 'submitButton']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
        <?= Html::a(Yii::t('app', 'Clear'), $form->action, ['class' => 'btn btn-default pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    
    var MovementSearch= new function(){
      
        this.init= function(){
            $(document).on('click', '#submitButton', function(e){
                e.preventDefault();
                $('AccountMovementSearch[from_date]').remove();
                $('AccountMovementSearch[to_date]').remove();
                var params= 'AccountMovementSearch%5BfromDate%5D='+$('#from-date').val()+'&AccountMovementSearch%5BtoDate%5D='+ $('#to-date').val();
                location.href= '<?= yii\helpers\Url::to(['/accounting/money-box-account/movements'])?>&id='+'<?=$money_box_account_id?>'+'&'+ params;
            });
        }
      
      
    };
    
    
</script>
<?= $this->registerJs('MovementSearch.init()')?>