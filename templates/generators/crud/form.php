<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */
?>

<div class="errors">

</div>

<?php
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'buildExportOptions')->checkbox();
?>

<div id="relation-form">

    <?php if (isset($generator->modelClass) && isset($generator->relationOptions)) : ?>

        <?php $relations = $generator->getModelRelations(); ?>

        <?php
        echo $this->render('//../templates/views/_crud_relations_form', [
            'relations' => $relations,
            'generator' => $generator,
        ]);
        ?>

<?php endif; ?>

</div>

<?php
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'viewPath');
echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'indexWidgetType')->dropDownList([
    'grid' => 'GridView',
    'list' => 'ListView',
]);
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
?>

<script>

    var Relation = new function () {

        var self = this;

        this.init = function () {

            self.addTableListener();

        }

        this.addTableListener = function () {

            $("#generator-modelclass").on("blur", function () {

                console.log($("#crud-generator").serialize());

                $.ajax({
                    data: $("#crud-generator").serialize(),
                    url: "<?php echo \yii\helpers\Url::to(["default/parse-crud-relations"]); ?>",
                    type: 'post',
                    dataType: 'json',
                    beforeSend: function () {

                    }
                }).done(function (response) {

                    if (response.status == "success") {

                        $(".errors").slideUp(200);
                        $("#relation-form").html(response.html).slideDown(200);

                    } else {

                        $("#relation-form").slideUp(200).html("");
                        $(".errors").html(response.html).slideDown(200);

                    }

                });

            });

        }

    }

</script>

<style>

    #relation-form{

        <?php if (!isset($generator->modelClass) || !isset($generator->relationOptions)) : ?>
            display: none;
        <?php endif; ?>

    }

    .errors{

        color: #f00;
        display: none;
        margin-bottom: 15px;

    }

</style>

<?php $this->registerJs("Relation.init();"); ?>