<?php

use yii\db\Migration;

/**
 * Class m190607_220020_CloseDataEditionAction
 */
class m190607_220020_CloseDataEditionAction extends Migration
{

    public function init()
    {
        $this->db = 'dbticket';
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('action', 'type', 'ENUM("ticket", "event", "data-edition")');

        $this->insert('action', [
            'name' => 'Finalizar edición de datos',
            'type' => 'data-edition',
            'slug' => 'finalizar-edicion-de-datos'
        ]);

        $actionId = $this->db->getLastInsertID();

        $status1 = \app\modules\ticket\models\Status::findOne(['name' => 'Cerrado (con éxito)']);

        if ($status1) {
            $status1->generate_action = true;
            $status1->action_id = $actionId;
            $status1->save();
        }

        $status2 = \app\modules\ticket\models\Status::findOne(['name' => 'Cerrado (sin éxito)']);

        if ($status2) {
            $status2->generate_action = true;
            $status2->action_id = $actionId;
            $status2->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('action', ['name' => 'Finalizar edición de datos']);

        $this->alterColumn('action', 'type', 'ENUM("ticket", "event")');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190607_220020_CloseDataEditionAction cannot be reverted.\n";

        return false;
    }
    */
}
