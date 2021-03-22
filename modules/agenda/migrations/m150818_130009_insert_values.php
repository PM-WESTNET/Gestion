<?php

use yii\db\Schema;
use yii\db\Migration;

class m150818_130009_insert_values extends Migration {

    public function init() {
        $this->db = 'dbagenda';
        parent::init();
    }

    public function safeUp() {

        //Task type inserts
        if (\Yii::$app->dbagenda->schema->getTableSchema('task_type') !== null) {
            $this->insert('task_type', [
                'task_type_id' => 1,
                'name' => 'Tarea global',
                'description' => 'Esta tarea sera notificada a todos los usuarios del sistema',
                'slug' => 'global',
            ]);
            $this->insert('task_type', [
                'task_type_id' => 2,
                'name' => 'Tarea por usuario',
                'description' => 'Esta tarea sera notificada a usuarios seleccionados',
                'slug' => 'by_user',
            ]);
        }

        //Status inserts
        if (\Yii::$app->dbagenda->schema->getTableSchema('status') !== null) {
            $this->insert('status', [
                'status_id' => 1,
                'name' => 'Creada',
                'description' => 'Tarea creada',
                'color' => 'normal',
                'slug' => 'created',
            ]);
            $this->insert('status', [
                'status_id' => 2,
                'name' => 'Pendiente',
                'description' => 'Tarea pendiente',
                'color' => 'warning',
                'slug' => 'pending',
            ]);
            $this->insert('status', [
                'status_id' => 3,
                'name' => 'En progreso',
                'description' => 'Tarea en progreso',
                'color' => 'info',
                'slug' => 'in_progress',
            ]);
            $this->insert('status', [
                'status_id' => 4,
                'name' => 'Detenida',
                'description' => 'Tarea detenida',
                'color' => 'danger',
                'slug' => 'stopped',
            ]);
            $this->insert('status', [
                'status_id' => 5,
                'name' => 'Completada',
                'description' => 'Tarea completada',
                'color' => 'success',
                'slug' => 'completed',
            ]);
        }

        //Category inserts
        if (\Yii::$app->dbagenda->schema->getTableSchema('category') !== null) {
            $this->insert('category', [
                'category_id' => 1,
                'name' => 'Genérica',
                'description' => 'Tarea genérica',
                'default_duration' => '02:00:00',
                'slug' => 'generic',
            ]);
        }
        
        //Event type inserts
        if (\Yii::$app->dbagenda->schema->getTableSchema('event_type') !== null) {
            $this->insert('event_type', [
                'event_type_id' => 1,
                'name' => 'Cambio de estado',
                'description' => 'Un usuario realizo un cambio de estado',
                'slug' => 'status_change',
            ]);
            $this->insert('event_type', [
                'event_type_id' => 2,
                'name' => 'Nota agregada',
                'description' => 'Un usuario agrego una nota',
                'slug' => 'note_added',
            ]);
        }
    }

    public function safeDown() {
        
    }

}
