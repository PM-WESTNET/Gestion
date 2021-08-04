<?php

namespace app\commands;

use yii\console\Controller;
use app\modules\sale\models\Customer;

class CustomerController extends Controller{

    public function actionUpdateInfoCustomer(){
        $customers = Customer::find()->where(['LIKE','name','%(%',false])->orWhere(['LIKE','lastname','%(%',false])->all();

        foreach ($customers as $key => $customer) {
            if((str_contains($customer->name, '(') || str_contains($customer->name, ')')) && (!str_contains($customer->lastname, '(') && !str_contains($customer->lastname, ')'))){

                $customer->description = str_replace(')','',substr($customer->name, (strpos($customer->name, '(')+1),(strpos($customer->name, ')')+1)));
                $customer->name = str_replace(substr($customer->name, strpos($customer->name, '('),(strpos($customer->name, ')')+1)),'',$customer->name);

            }else if((str_contains($customer->lastname, '(') || str_contains($customer->lastname, ')')) && (!str_contains($customer->name, '(') && !str_contains($customer->name, ')'))){
                
                $customer->description = str_replace(')','',substr($customer->lastname, (strpos($customer->lastname, '(')+1),(strpos($customer->lastname, ')')+1)));
                $customer->lastname = str_replace(substr($customer->lastname, strpos($customer->lastname, '('),(strpos($customer->lastname, ')')+1)),'',$customer->lastname);

            }else{
                $description_one = str_replace(')','',substr($customer->name, (strpos($customer->name, '(')+1),(strpos($customer->name, ')')+1)));
                $description_two = str_replace(')','',substr($customer->lastname, (strpos($customer->lastname, '(')+1),(strpos($customer->lastname, ')')+1)));

                $customer->name = str_replace(substr($customer->name, strpos($customer->name, '('),(strpos($customer->name, ')')+1)),'',$customer->name);
                $customer->lastname = str_replace(substr($customer->lastname, strpos($customer->lastname, '('),(strpos($customer->lastname, ')')+1)),'',$customer->lastname);
                $customer->description = $description_one . ' - ' . $description_two;
            }

            $customer->save(false);
            echo "******************************************************\n"
                 ."Nombre: " . $customer->name . "\n" 
                 ."Apellido: " . $customer->lastname . "\n" 
                 ."Descripción: " . $customer->description . "\n"
                 ."\n******************************************************";
        }
    }
}