<?php

use yii\db\Schema;
use yii\db\Migration;
use webvimark\modules\UserManagement\models\rbacDB\Permission;
use webvimark\modules\UserManagement\models\rbacDB\AuthItemGroup;

class m151029_143854_bill_permissions extends Migration
{
    public function up()
    {
        
        $group = new AuthItemGroup;
        $group->code = 'sale-bill-permissions';
        $group->name = Yii::t('app', 'Sale Bill Group');
        
        if($group->save()){
        
            //Permisos de comprobantes: alta
            Permission::create('user-can-create-bill', Yii::t('app', 'User can create invoices'), 'sale-bill-permissions');
            Permission::create('user-can-create-order', Yii::t('app', 'User can create orders'), 'sale-bill-permissions');
            Permission::create('user-can-create-budget', Yii::t('app', 'User can create budgets'), 'sale-bill-permissions');
            Permission::create('user-can-create-delivery-note', Yii::t('app', 'User can create delivery notes'), 'sale-bill-permissions');
            Permission::create('user-can-create-credit', Yii::t('app', 'User can create credit notes'), 'sale-bill-permissions');
            Permission::create('user-can-create-debit', Yii::t('app', 'User can create debit notes'), 'sale-bill-permissions');

            //Permisos de comprobantes: modificacion
            Permission::create('user-can-update-bill', Yii::t('app', 'User can update invoices'), 'sale-bill-permissions');
            Permission::create('user-can-update-order', Yii::t('app', 'User can update orders'), 'sale-bill-permissions');
            Permission::create('user-can-update-budget', Yii::t('app', 'User can update budgets'), 'sale-bill-permissions');
            Permission::create('user-can-update-delivery-note', Yii::t('app', 'User can update delivery notes'), 'sale-bill-permissions');
            Permission::create('user-can-update-credit', Yii::t('app', 'User can update credit notes'), 'sale-bill-permissions');
            Permission::create('user-can-update-debit', Yii::t('app', 'User can update debit notes'), 'sale-bill-permissions');
            
            //Permisos de comprobantes: eliminacion
            Permission::create('user-can-delete-bill', Yii::t('app', 'User can delete invoices'), 'sale-bill-permissions');
            Permission::create('user-can-delete-order', Yii::t('app', 'User can delete orders'), 'sale-bill-permissions');
            Permission::create('user-can-delete-budget', Yii::t('app', 'User can delete budgets'), 'sale-bill-permissions');
            Permission::create('user-can-delete-delivery-note', Yii::t('app', 'User can delete delivery notes'), 'sale-bill-permissions');
            Permission::create('user-can-delete-credit', Yii::t('app', 'User can delete credit notes'), 'sale-bill-permissions');
            Permission::create('user-can-delete-debit', Yii::t('app', 'User can delete debit notes'), 'sale-bill-permissions');
            
            //Permisos de comprobantes: reapertura
            Permission::create('user-can-open-bill', Yii::t('app', 'User can open invoices'), 'sale-bill-permissions');
            Permission::create('user-can-open-order', Yii::t('app', 'User can open orders'), 'sale-bill-permissions');
            Permission::create('user-can-open-budget', Yii::t('app', 'User can open budgets'), 'sale-bill-permissions');
            Permission::create('user-can-open-delivery-note', Yii::t('app', 'User can open delivery notes'), 'sale-bill-permissions');
            Permission::create('user-can-open-credit', Yii::t('app', 'User can open credit notes'), 'sale-bill-permissions');
            Permission::create('user-can-open-debit', Yii::t('app', 'User can open debit notes'), 'sale-bill-permissions');
            
            return true;
            
        }else{
            return false;
        }
    }

    public function down()
    {
        $this->delete('auth_item', ['group_code' => 'sale-bill-permissions']);
        $this->delete('auth_item_group', ['code' => 'sale-bill-permissions']);

        return true;
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
