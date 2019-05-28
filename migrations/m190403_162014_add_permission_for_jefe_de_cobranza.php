<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 03/04/19
 * Time: 16:20
 */

use yii\db\Migration;
use app\modules\ticket\models\Status;
use app\modules\ticket\models\Action;
use app\modules\agenda\models\Category;
use app\modules\agenda\models\TaskType;
use app\modules\ticket\models\Schema;
use webvimark\modules\UserManagement\models\User;
use app\modules\ticket\components\schemas\SchemaCobranza;
use webvimark\modules\UserManagement\models\rbacDB\Permission;
use webvimark\modules\UserManagement\models\rbacDB\Role;

class m190403_162014_add_permission_for_jefe_de_cobranza extends Migration
{

    public function safeUp()
    {

        if(Permission::findOne('cashing_manager') === null) {
            Role::assignRoutesViaPermission('collection_manager', 'cashing_manager', [
                '/sale/customer/cashing-panel',
                '/ticket/observation/build-observation',
                '/ticket/observation/create',
                '/ticket/observation/index',
                '/ticket/schema/create',
                '/ticket/schema/delete',
                '/ticket/schema/index',
                '/ticket/schema/update',
                '/ticket/schema/view',
                '/ticket/status/create',
                '/ticket/status/delete',
                '/ticket/status/index',
                '/ticket/status/update',
                '/ticket/status/view',
                '/ticket/ticket/assign-ticket-to-user',
                '/ticket/ticket/close',
                '/ticket/ticket/collection-tickets',
                '/ticket/ticket/create',
                '/ticket/ticket/create-and-assign-user',
                '/ticket/ticket/customers-has-cobranza-ticket',
                '/ticket/ticket/delete',
                '/ticket/ticket/edit-status',
                '/ticket/ticket/get-observation-form',
                '/ticket/ticket/get-observations',
                '/ticket/ticket/history',
                '/ticket/ticket/index',
                '/ticket/ticket/list',
                '/ticket/ticket/observation',
                '/ticket/ticket/open-tickets',
                '/ticket/ticket/reopen',
                '/ticket/ticket/status-has-event-action',
                '/ticket/ticket/update',
                '/ticket/ticket/view',
                '/ticket/type/create',
                '/ticket/type/delete',
                '/ticket/type/get-categories',
                '/ticket/type/index',
                '/ticket/type/update',
                '/ticket/type/view',
                '/user-management/user-visit-log/delete'
            ], 'canAdminCashingTickets', 'Tickets');
        }
    }

    public function safeDown()
    {
    }
}