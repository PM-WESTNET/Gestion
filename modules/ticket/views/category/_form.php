<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\modules\ticket\TicketModule;
use yii\helpers\ArrayHelper;
use webvimark\modules\UserManagement\models\User;
use app\modules\ticket\models\Schema;
use app\modules\ticket\models\Category;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'parent_id')->widget(Select2::class,[
        'data' => yii\helpers\ArrayHelper::map( Category::getForSelect(), 'category_id', 'name' ),
        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]);
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php if ($model->isNewRecord) : ?>
        <?= $form->field($model, 'slug')->textInput(['maxlength' => 45]) ?>
    <?php endif; ?>

    <?= $form->field($model, 'schema_id')->widget(Select2::class, [
            'data' => Schema::getForSelect()
    ])?>

    <?= $form->field($model, 'notify')->checkbox(['id'=>'category_notify'])  ?>

    <div id="external_user_id_div">

    </div>
    <?= $form->field($model, 'external_user_id')->dropDownList([], ['id'=>'category_external_user_id']) ?>

    <div id="responsible_user_id_div">
        <?= $form->field($model, 'responsible_user_id')->widget(Select2::class, [
            'data' => ArrayHelper::map(User::find()->where(['status' => 1])->all(), 'id', 'username'),
            'options' => ['placeholder' => Yii::t('app', 'Select ...')],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ], [
            'id' => 'responsible_user_id'
        ])?>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? TicketModule::t('app', 'Create') : TicketModule::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    var CategoryForm = new function(){
        this.init = function(){
            $(document).on('click', '#category_notify', function(){
                CategoryForm.users();
            });

            CategoryForm.users();
            CategoryForm.loadUsers();

            $('#external_user_id_div').addClass('hidden');
            $('#responsible_user_id_div').removeClass('hidden');
        }

        this.users = function(){
            if( $('#category_notify').is(':checked') ) {
                $('.field-category_external_user_id').show();
                $('#category_external_user_id').removeAttr('disabled');
                $('#external_user_id_div').removeClass('hidden');
                $('#responsible_user_id_div').addClass('hidden');
            } else {
                $('.field-category_external_user_id').hide();
                $('#category_external_user_id').val('');
                $('#external_user_id_div').addClass('hidden');
                $('#responsible_user_id_div').removeClass('hidden');
            }
        }

        this.loadUsers = function(){
            $.ajax({
                url: '<?php echo Url::toRoute(['/ticket/category/get-external-users'])?>',
                method: 'GET',
                dataType: 'json',
                success: function(data){
                    var $select = $('#category_external_user_id');
                    $select.find('option').remove();
                    $('<option>').val('').text('<?php echo Yii::t('app', 'Select')  ?>').appendTo($select);

                    $.each(data, function(key,item){
                        $('<option>').val(item.id).text(item.nombre).appendTo($select);
                    });
                    $select.val(<?php echo $model->external_user_id ?>);
                }
            });
        }
    }
</script>
<?php $this->registerJs("CategoryForm.init();"); ?>