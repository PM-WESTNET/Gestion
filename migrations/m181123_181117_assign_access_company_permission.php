<?php

use yii\db\Migration;
use app\modules\westnet\models\Vendor;
use app\modules\sale\models\Company;

class m181123_181117_assign_access_company_permission extends Migration
{
    public function up()
    {
        $external_vendors = Vendor::getExternalVendors();

        $company = Company::findOne(['name' => 'Westnet']);
        foreach ($external_vendors as $ext_vendor){
            $user = $ext_vendor->user;
            if($user){
                $user->link('companies', $company);
            }
        }
    }

    public function down()
    {
        $external_vendors = Vendor::getExternalVendors();
        foreach ($external_vendors as $ext_vendor){
            $user = $ext_vendor->user;
            if($user){
                $user->unlinkAll('companies', true);
            }
        }
    }
}
