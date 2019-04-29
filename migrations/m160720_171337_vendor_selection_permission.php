<?php

use yii\db\Migration;
use webvimark\modules\UserManagement\models\rbacDB\AuthItemGroup;
use webvimark\modules\UserManagement\models\rbacDB\Permission;

class m160720_171337_vendor_selection_permission extends Migration
{
    public function up()
    {
        
        if(Yii::$app->getModule('user-management')){
            
            $group = AuthItemGroup::find()->where('code="Administrative"');

            if($group){

                //Permisos de comprobantes: alta
                Permission::create('user-can-select-vendor', Yii::t('app', 'Usuario puede seleccionar vendedor'), 'Administrative');
                return true;

            }else{
                return false;
            }
            
        }
        
    }

    public function down()
    {
        echo "m160720_171337_vendor_selection_permission cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
