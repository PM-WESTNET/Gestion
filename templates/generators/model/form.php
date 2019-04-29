<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\form\Generator */
?>

<div class="errors">
    <?php if (!empty($generator->getErrors('i18n'))) : ?>
        <?php foreach ($generator->getErrors('i18n') as $error) : ?>
            <?php echo $error; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
echo $form->field($generator, 'tableName');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'ns');
echo $form->field($generator, 'baseClass');
echo $form->field($generator, 'db');
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'generateRelations')->checkbox();
?>

<div id="relation-form">

    <?php if (isset($generator->tableName) && isset($generator->relationOptions)) : ?>

        <?php $relations = $generator->generateRelations(); ?>

        <?php
        echo $this->render('//../templates/views/_model_relations_form', [
            'relations' => !empty($relations[$generator->tableName]) ? $relations[$generator->tableName] : [],
            'generator' => $generator,
        ]);
        ?>

<?php endif; ?>

</div>

<?php
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryNs');
echo $form->field($generator, 'queryClass');
echo $form->field($generator, 'queryBaseClass');
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

            $("#generator-tablename").on("blur", function () {

                $.ajax({
                    data: $("#model-generator").serialize(),
                    url: "<?php echo \yii\helpers\Url::to(["default/parse-model-relations"]); ?>",
                    type: 'post',
                    dataType: 'json',
                    beforeSend: function () {

                    }
                }).done(function (response) {

                    if (response.status == "success") {

                        $(".errors").slideUp(200);
                        $("#relation-form").html(response.html).slideDown(200);

                    } else {

                        $("#relation-form").slideDown(200).html("");
                        $(".errors").html(response.html).slideDown(200);

                    }

                }).error(function () {

                    console.log("Ajax request error.");

                });

            });

        }

    }

</script>

<style>

    #relation-form{

        <?php if (!isset($generator->tableName) || !isset($generator->relationOptions)) : ?>
            display: none;
        <?php endif; ?>

    }

    .errors{

        color: #f00;
        <?php if (empty($generator->getErrors('i18n'))) : ?>
            display: none;
        <?php endif; ?>
        margin-bottom: 15px;

    }

</style>

<?php $this->registerJs("Relation.init();"); ?>