<div class="validation-email">
    <table width="600">
        <tr>
            <td style="height:80px; width: 50%; padding-bottom: 20px; padding-top: 20px; border-bottom: 1px solid gray; text-align: center">
                <?php if(!empty(Yii::$app->view->params['image'])):?>
                    <img style="height: 100%; display: inline-block;"
                         src="<?= $message->embed(Yii::$app->view->params['image'], ['contentType' => 'image/jpeg']) ?>" alt="Logo">
                <?php else:?>
                    <img style="height: 100%; display: inline-block;"
                         src="<?= $message->embed(Yii::getAlias('@app/web/images/').'logo-westnet.jpg', ['contentType' => 'image/jpeg']) ?>" alt="Logo">
                <?php endif;?>
            </td>
        </tr>
        <!-- Contenido -->
        <tr>
            <td style="width: 100%; padding: 20px; color: gray; font-size: 16px; font-family: Arial, sans-serif; line-height: 24px;">
                <p class="text-center">
                    <h2>Gracias por registrarte en nuestra app!!!</h2>

                    <p>Para completar el proceso de registro introduce el siguiente código en la app</p>

                    <h3 style="text-align: center">Código de Validación:</h3>
                    <h2 style="text-align: center"><?= Yii::$app->view->params['code'] ?></h2>
                </p>
            </td>
        </tr>
        <!-- Footer -->
        <?php if (isset($footer)) { ?>
            <tr>
                <td style="background-color: #C1C1C1; color: white; font-size: 12px; line-height: 16px; padding:10px; font-family: Arial, sans-serif;">
                    <p><? //$footer?></p>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
