<?php

use yii\db\Schema;
use yii\db\Migration;

class m151020_135813_update_values extends Migration
{
    public function init() {
        $this->db = 'dbagenda';
        parent::init();
    }
    
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        //Actualiza el nombre de la tarea "creada" a "nueva"
        if (\Yii::$app->dbagenda->schema->getTableSchema('status') !== null) {
            $this->update('status', [
                'name' => 'Nueva',
                'description' => 'Tarea nueva',
                'color' => 'normal',
                'slug' => 'created',
            ], [
                'status_id' => 1
            ]);
        }
    }

    public function safeDown()
    {
        //Vuelve el nombre de la tarea "nueva" a "creada"
        if (\Yii::$app->dbagenda->schema->getTableSchema('status') !== null) {
            $this->update('status', [
                'name' => 'Creada',
                'description' => 'Tarea creada',
                'color' => 'normal',
                'slug' => 'created',
            ], [
                'status_id' => 1
            ]);
        }
    }
    
}
