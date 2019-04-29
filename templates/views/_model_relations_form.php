<?php
/**
 * var array $relations
 */
?>

<?php if(!empty($relations)) : ?>

    <div class="well">

        <h3 style="margin-top: 0;">Relations</h3>

        <?php foreach($relations as $relationName => $relation) : ?>

            <div>

                <strong class="text-primary"><?php echo $relationName; ?> Relation</strong>
                
                <ul class="bordered list-unstyled" style="padding-left: 15px; margin: 5px 0px;">
                    
                    <li>Type <label class="label label-info"><?php echo $relation['type']; ?></label></li>
                    
                    <li>Related model <strong class="text-info"><?php echo $relation['1']; ?></strong></li>
                    
                    <li class="form-group">
                        <div class="checkbox">
                            <?php echo \yii\helpers\Html::hiddenInput('Generator[relationOptions][' . $relationName . '][build]', 0); ?>
                            <?php 
                            
                            if(isset($relation['implementation']['build'])){
                                $buildCheck = ($relation['implementation']['build'] == 1) ? true : false ;
                            }else{
                                $buildCheck = false;
                            }
                            echo \yii\helpers\Html::checkbox('Generator[relationOptions][' . $relationName . '][build]', $buildCheck, [
                                'label' => 'Build this relation'
                            ]);?>
                            <small class="text-muted">Whether generator should build code for this relation</small>
                        </div>
                    </li>
                    
                    <li class="form-group">
                        <div class="checkbox">
                            <?php echo \yii\helpers\Html::hiddenInput('Generator[relationOptions][' . $relationName . '][strength]', 0); ?>
                            <?php 
                            
                            if(isset($relation['implementation']['isDeleteable'])){
                                $strengthCheck = ($relation['implementation']['isDeleteable'] == 0) ? true : false ;
                            }else{
                                $strengthCheck = false;
                            }
                            
                            echo \yii\helpers\Html::checkbox('Generator[relationOptions][' . $relationName . '][strength]', $strengthCheck, [
                                'label' => 'Strong relation'
                            ]);?>
                            <small class="text-muted">If checked, model will not be deleteable without deleting the relationed model first</small>
                        </div>
                    </li>
                    
                    <li class="form-group">
                        <div>
                            <label for="representation">Showed as</label>
                            <?php 
                            $selected = null;
                            if(isset($relation['implementation']['representation'])){
                                $selected = $relation['implementation']['representation'];
                            }
                            
                            echo \yii\helpers\Html::dropDownList('Generator[relationOptions][' . $relationName . '][representation]', $selected, $generator->fetchRelationTypes($relation['type']), [
                                'prompt' => 'Select an option...',
                                'class' => 'form-control'
                            ]);?>
                        </div>
                    </li>
                    
                </ul>

            </div>
                    
            <hr>

        <?php endforeach; ?>

    </div>

<?php else : ?>

        <p class="text-muted">
            No se encontraron relaciones
        </p>

<?php endif; ?>


<style>
    
    .bordered{
        display: block;
        border-left: 1px solid #ccc;
    }
    
    small{
        
        display: block;
        
    }

    .relation-list{
        
        padding: 5px 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        
    }

</style>