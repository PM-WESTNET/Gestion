<?php

use yii\db\Migration;
use app\modules\ticket\models\Schema;

class m190906_145252_add_category_gestion_de_ads extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $schema = Schema::findOne(['name' => 'Default']);
        $this->insert('category', [
            'name' => 'Gestión de ADS',
            'slug' => 'gestion-de-ads',
            'schema_id' => $schema ? $schema->schema_id : null
        ]);
    }

    public function safeDown()
    {
        $this->delete('category', ['slug' => 'gestion-de-ads']);
    }
}
