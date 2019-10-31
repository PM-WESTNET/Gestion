<?php

use yii\db\Migration;
use app\modules\config\models\Category;
use app\modules\ticket\models\Status;

class m191009_173434_add_config_items_for_ticket_status extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Ticket']);

        $status_pago = Status::find()->where(['name' => 'Pagó'])->one();
        $status_pago_sin_gestionar = Status::find()->where(['name' => 'Pagó sin gestionar'])->one();

        $this->insert('item', [
            'attr' => 'ticket_status_pago',
            'type' => 'textInput',
            'label' =>"Estado de ticket pago",
            'description' => "Número entero. Indica el id del estado de ticket de cobranza 'Pagó'",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => $status_pago ? $status_pago->status_id : 0
        ]);

        $this->insert('item', [
            'attr' => 'ticket_status_pago_sin_gestionar',
            'type' => 'textInput',
            'label' =>"Estado de ticket pago sin gestionar",
            'description' => "Número entero. Indica el id del estado de ticket de cobranza 'Pagó sin gestionar'",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => $status_pago_sin_gestionar ? $status_pago_sin_gestionar->status_id : 0
        ]);
    }

    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'ticket_status_pago_sin_gestionar'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'ticket_status_pago'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}
