<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 03/04/19
 * Time: 14:56
 */

use yii\db\Migration;
use app\modules\ticket\models\Status;
use app\modules\ticket\models\Action;
use app\modules\agenda\models\Category;
use app\modules\agenda\models\TaskType;
use app\modules\ticket\models\Schema;
use webvimark\modules\UserManagement\models\User;

class m190403_145656_erase_status_from_status_table extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $status = Status::find()->where(['name' => 'No va a pagar'])->one();
        $new_status = Status::find()->where(['name' => 'Informado'])->one();

        foreach($status->tickets as $ticket) {
            $this->update('ticket', ['status_id' => $new_status->status_id], ['ticket_id' => $ticket->ticket_id]);
        }

        $this->delete('schema_has_status', ['status_id' => $status->status_id]);
        $this->delete('status', ['name' => 'No va a pagar']);

    }

    public function safeDown()
    {
       $this->insert('status', [
           'name' => 'No va a pagar',
           'description' => 'Informa que no va a pagar',
           'is_open' => 0,
       ]);
    }
}