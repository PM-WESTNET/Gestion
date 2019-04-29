<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use app\modules\sale\modules\contract\models\PlanFeature;
?>
<div class="_feature_plan">
    <tr>
        <td>
            <?php
                $parent_id=$model->parent_id;
                 echo HtmlPurifier::process(PlanFeature::findOne($parent_id)->name);
            ?>
        </td>    
        <td>
             <?=
                 HtmlPurifier::process($model->name);
              ?>    
        </td>

    </tr>    
</div>