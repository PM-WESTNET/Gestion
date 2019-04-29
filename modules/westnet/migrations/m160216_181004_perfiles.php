<?php

use yii\db\Schema;
use yii\db\Migration;

class m160216_181004_perfiles extends Migration
{
    public function up()
    {
        $this->insert('profile_class', [
            'name' => 'Participación en avisos publicitarios',
            'data_type' => 'checkbox',
            'status' => 'enabled',
            'hint' => '',
            'searchable' => 0,
            'data_min' => '',
            'data_max' => NULL,
            'pattern' => '',
            'order' => NULL
        ]);
        $this->insert('profile_class', [
            'name' => 'Participación en aviso por portal de mesa de ayuda',
            'data_type' => 'checkbox',
            'status' => 'enabled',
            'hint' => '',
            'searchable' => 0,
            'data_min' => '',
            'data_max' => NULL,
            'pattern' => '',
            'order' => NULL
        ]);

        $this->insert('profile_class', [
            'name' => 'Participación en las campañas por mensajes de texto',
            'data_type' => 'checkbox',
            'status' => 'enabled',
            'hint' => '',
            'searchable' => 0,
            'data_min' => '',
            'data_max' => NULL,
            'pattern' => '',
            'order' => NULL
        ]);

    }

    public function down()
    {
        $this->delete('profile_class', ['name' => 'Participación en las campañas por mensajes de texto']);
        $this->delete('profile_class', ['name' => 'Participación en aviso por portal de mesa de ayuda']);
        $this->delete('profile_class', ['name' => 'Participación en avisos publicitarios']);

        return false;
    }

}
