<?php

use app\modules\config\models\Category;
use yii\db\Migration;
use yii\db\Query;

class m160615_195300_config_mesa_server extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $this->createConfig();
    }

    public function down()
    {

        $this->deleteConfig();

        return false;
    }

    private function createConfig()
    {

        $category = Category::findOne(['name' => 'Westnet']);

        $this->insert('item', [
            'attr' => 'mesa_server_address',
            'type' => 'textInput',
            'label' => 'Direccion del servidor de mesa.',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => ''
        ]);

    }

    private function deleteConfig()
    {
        $this->delete('item', ['attr' => 'mesa_server_address']);
    }
}
