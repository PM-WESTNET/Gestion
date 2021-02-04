<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 14/08/19
 * Time: 17:36
 */
use yii\widgets\ActiveForm;


$search = new \app\modules\accounting\models\search\AccountMovementSearch();
$search->date= '';
?>

<div class="panel panel-search" >
    <div class="panel-body">
        <?php
        $form= ActiveForm::begin(['id' => 'movements_search_form'])
        ?>
        <div class="row">
            <div class="col-lg-3">
                <?php
                echo $form->field($search, 'date')->widget(\kartik\date\DatePicker::class, [
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy',
                        'autoclose' => true,
                    ],
                    'size' => 'sm'

                ])?>
            </div>
            <div class="col-lg-3">
                <?php
                echo $form->field($search, 'description')->textInput(['class' => 'form-control input-sm']) ?>
            </div>
            <div class="col-lg-4">
                <?php
                echo $form->field($search, 'cuit')->textInput(['class' => 'form-control input-sm']) ?>
            </div>

            <div class="col-lg-2">
                <br>
                <?php echo \yii\helpers\Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-sm btn-default', 'id' => 'btn-movement-search' ])?>
            </div>
            <?php ActiveForm::end()?>
        </div>

    </div>
</div>
