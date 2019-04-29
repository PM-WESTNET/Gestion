<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use app\modules\sale\models\CustomerClass;
use app\modules\sale\models\Customer;
?>
<div class="_class">
    <tr bgcolor="<?= 'white'//CustomerClass::findOne($model->customer_class_id)->colour; ?>" >
        
        <td>
             <?=
                 HtmlPurifier::process(CustomerClass::findOne($model->customer_class_id)->name);
              ?>    
        </td>
        <td>
             <?= 
                 date('d/m/y h:m', $model->date_updated);
                //HtmlPurifier::process($model->date_updated) ;
              ?>    
        </td>
        
        <td>
             <?php 
                 $customerClass= CustomerClass::findOne($model->customer_class_id);
                 $duration= $customerClass->days_duration;
                 $id_actual=Customer::findOne($model->customer_id)->getCustomerClass()->customer_class_id;
                 $expiration= ($duration * 60 * 60 * 24) + $model->date_updated;
                 if($id_actual!=$customerClass->customer_class_id){
                     echo "No corresponde";
                 }
                 else if($duration==0){
                     echo'No expira';
                 }
                 else{
                     echo date('d/m/y h:m', $expiration);
                 }
                 
              ?>    
        </td>
        
        
    </tr>    
</div>