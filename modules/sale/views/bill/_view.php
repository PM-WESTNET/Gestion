<div class="row">
	<div class="col-xs-12">
		<?php
			use yii\helpers\Url;
			use yii\helpers\Html;

			$view = $model->billType->view;

			
			

			if(isset($form) && $form === true){
			    echo $this->render("types/form/$view",['model'=>$model,'detailsProvider'=>$detailsProvider]);
			}else{
			    echo $this->render("types/view/$view",['model'=>$model,'detailsProvider'=>$detailsProvider]);
			}
		?>
		
	</div>
</div>
