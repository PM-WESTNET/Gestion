<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 19/03/19
 * Time: 14:51
 */

use yii\db\Migration;
use app\modules\ticket\models\Status;

class m190319_145050_add_tables_for_ticket_status_schema extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $this->createTable('schema', [
            'schema_id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);

        $this->addColumn('category', 'schema_id', $this->integer());

        $this->addForeignKey('fk_category_schema_id', 'category', 'schema_id', 'schema', 'schema_id');

        $this->createTable('schema_has_status', [
            'schema_has_status_id' => $this->primaryKey(),
            'schema_id' => $this->integer(),
            'status_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk_schema_has_status_schema_id', 'schema_has_status', 'schema_id', 'schema', 'schema_id');
        $this->addForeignKey('fk_schema_has_status_status_id', 'schema_has_status', 'status_id', 'status', 'status_id');

        $this->insert('schema', [
            'name' => 'Default'
        ]);

        $schema_id = $this->getDb()->lastInsertID;
        $statuses = Status::find()->all();

        foreach ($statuses as $status) {
            $this->insert('schema_has_status', [
                'schema_id' => $schema_id,
                'status_id' => $status->status_id
            ]);
        }

    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_schema_has_status_status_id', 'schema_has_status');
        $this->dropForeignKey('fk_schema_has_status_schema_id', 'schema_has_status');

        $this->dropTable('schema_has_status');

        $this->dropForeignKey('fk_category_schema_id', 'category');

        $this->dropColumn('category', 'schema_id');

        $this->dropTable('schema');
    }
}