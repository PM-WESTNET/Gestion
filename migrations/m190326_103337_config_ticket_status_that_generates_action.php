<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 26/03/19
 * Time: 10:37
 */

use yii\db\Migration;
use app\modules\ticket\models\Status;
use app\modules\ticket\models\Action;
use app\modules\agenda\models\Category;
use app\modules\agenda\models\TaskType;
use app\modules\ticket\models\Schema;
use webvimark\modules\UserManagement\models\User;

class m190326_103337_config_ticket_status_that_generates_action extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $status = Status::find()->where(['name' => 'Compromiso de pago'])->one();
        $action = Action::find()->where(['slug' => 'crear-evento-de-cobranza-en-agenda'])->one();
        $task_category = Category::find()->where(['slug' => 'generic'])->one();
        $task_type = TaskType::find()->where(['slug' => 'by_user'])->one();
        $task_status = \app\modules\agenda\models\Status::find()->where(['slug' => 'created'])->one();

        $this->insert('status_has_action', [
            'status_id' => $status->status_id,
            'action_id' => $action->action_id,
            'text_1' => 'Cliente tiene un compromiso de pago',
            'task_category_id' => $task_category->category_id,
            'task_type_id' => $task_type->task_type_id,
            'task_status_id' => $task_status->status_id,
            'task_priority' => 2,
            'task_time' => '13:00',
        ]);

        $this->insert('status', [
            'name' => 'Baja',
            'description' => 'Se requiere la baja del servicio',
            'is_open' => 1,
            'generate_action' => 1
        ]);

        $schema = Schema::find()->where(['name' => 'Cobranza'])->one();
        $status = Status::find()->where(['name' => 'Baja'])->one();

        $this->insert('schema_has_status', [
           'schema_id' => $schema->schema_id,
            'status_id' => $status->status_id
        ]);

        $action = Action::find()->where(['slug' => 'crear-ticket-baja-derivada-de-cobranza'])->one();
        $ticket_category = \app\modules\ticket\models\Category::find()->where(['name' => 'Baja Derivada Cobranzas'])->one();
        $ticket_status = Status::find()->where(['name'  => 'nuevo'])->one();

        $this->insert('status_has_action', [
            'status_id' => $status->status_id,
            'action_id' => $action->action_id,
            'text_1' => 'Baja requerida',
            'text_2' => 'Se requiere la baja del servicio',
            'ticket_category_id' => $ticket_category->category_id,
            'ticket_status_id' => $ticket_status->status_id,
        ]);

        //asignacion de responsables a categorias de ticket
        $category_baja_derivada_de_cobranza = \app\modules\ticket\models\Category::find()->where(['name' => 'Baja Derivada Cobranzas'])->one();
        $user_amarcozzi = User::find()->where(['username' => 'amarcozzi'])->one();

        $this->update('category', [
            'responsible_user_id' => $user_amarcozzi->id
        ], [
            'category_id' => $category_baja_derivada_de_cobranza->category_id
        ]);

        $category_nota_de_credito = \app\modules\ticket\models\Category::find()->where(['name' => 'Nota de crédito'])->one();
        $category_facturacion = \app\modules\ticket\models\Category::find()->where(['name' => 'Facturación'])->one();
        $user_frios = User::find()->where(['username' => 'frios'])->one();

        $this->update('category', [
            'responsible_user_id' => $user_frios->id
        ], [
            'category_id' => $category_nota_de_credito->category_id
        ]);

        $this->update('category', [
            'responsible_user_id' => $user_frios->id
        ], [
            'category_id' => $category_facturacion->category_id
        ]);

    }

    public function safeDown()
    {
        $status = Status::find()->where(['name' => 'Baja'])->one();
        $action = Action::find()->where(['slug' => 'crear-ticket-baja-derivada-de-cobranza'])->one();

        $this->delete('status_has_action', [
            'status_id' => $status->status_id,
            'action_id' => $action->action_id,
        ]);

        $schema = Schema::find()->where(['name' => 'Cobranza'])->one();
        $status = Status::find()->where(['name' => 'Baja'])->one();

        $this->delete('schema_has_status', [
            'schema_id' => $schema->schema_id,
            'status_id' => $status->status_id
        ]);

        $this->delete('status', [
            'name' => 'Baja',
        ]);

        $status = Status::find()->where(['name' => 'Compromiso de pago'])->one();
        $action = Action::find()->where(['slug' => 'crear-evento-de-cobranza-en-agenda'])->one();

        $this->delete('status_has_action', [
            'status_id' => $status->status_id,
            'action_id' => $action->action_id,
        ]);
    }
}