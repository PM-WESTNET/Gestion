<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var $this yoo\web\View
 */

$this->title= "Importar Archivo de Pago Fácil";
$this->params['breadcrumbs'][]= ['label' => Yii::t('app', 'Pago Fácil Files'), 'url' => ['pagofacil-payments-index']];
$this->params['breadcrumbs'][]= $this->title;
?>
<div class="pagofacil-import">
     <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        
    </div>
    <div class="row">
        <p>Seleccione un archivo de transmisión enviado por Pago Fácil para importar los pagos externos registrados. El proceso de importacion puede tardar.</p>
    </div>
    <div class="row">
    
        <?php $form= ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        
        <?= $form->field($model, 'file')->fileInput() ?>

        <?= $this->render('@app/modules/accounting/views/money-box-account/_selector', ['model' => $model, 'form' => $form, 'style' => 'horizontal']); ?>

        <?= Html::submitButton('Importar', ['class' => 'btn btn-success', 'id' => 'import'])?>

        <?php ActiveForm::end();?>  
        
    </div>
    
</div>

<script>

    var PagoFacilIndex= new function(){
        
        this.init= function(){
            $(document).on('click', '#import', function(){
               $('#import').html('Importando...'); 
            });
        }
        
        
    }



</script>
<?php $this->registerJs('PagoFacilIndex.init()');?>
    
    

