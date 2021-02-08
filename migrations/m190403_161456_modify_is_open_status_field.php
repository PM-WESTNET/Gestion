<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 03/04/19
 * Time: 16:02
 */

use yii\db\Migration;
use app\modules\ticket\models\Status;
use app\modules\ticket\models\Action;
use app\modules\agenda\models\Category;
use app\modules\agenda\models\TaskType;
use app\modules\ticket\models\Schema;
use webvimark\modules\UserManagement\models\User;
use app\modules\ticket\components\schemas\SchemaCobranza;

class m190403_161456_modify_is_open_status_field extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $statuses = SchemaCobranza::getSchemaStatuses();

        foreach ($statuses as $status) {
            if($status->name == 'Baja' || $status->name == 'Pagó') {
                $this->update('status', ['is_open' => 0], ['status_id' => $status->status_id]);
            } else {
                $this->update('status', ['is_open' => 1], ['status_id' => $status->status_id]);
            }
        }
    }

    public function safeDown()
    {
    }
}