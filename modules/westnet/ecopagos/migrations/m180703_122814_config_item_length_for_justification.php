<?php

use yii\db\Migration;

class m180703_122814_config_item_length_for_justification extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $category = \app\modules\config\models\Category::findOne(['name' => 'Ecopago']);

        $this->insert('item', [
            'attr' => 'justification_length',
            'type' => 'textInput',
            'label' => 'Cantidad de caracteres',
            'description' => 'Cantidad de caracteres mínimos para la justificación de una reimpresion o cancelación',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => '100'
        ]);
    }

    public function down()
    {
        $this->delete('item', [
            'attr' => 'justification_length',
        ]);
    }
}
