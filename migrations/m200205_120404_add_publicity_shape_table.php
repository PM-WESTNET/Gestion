<?php

use yii\db\Migration;

/**
 * Class m200205_120404_add_publicity_shape_table
 */
class m200205_120404_add_publicity_shape_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('publicity_shape', [
            'publicity_shape_id' => $this->primaryKey(),
            'name' => $this->string(),
            'slug' => $this->string(),
            'status' => "ENUM('enabled', 'disabled')"
        ]);

        $this->insert('publicity_shape', [
            'name' => 'Banner',
            'slug' => 'banner',
            'status' => 'enabled'
        ]);

        $this->insert('publicity_shape', [
            'name' => 'Cartel',
            'slug' => 'poster',
            'status' => 'enabled'
        ]);

        $this->insert('publicity_shape', [
            'name' => 'Página Web',
            'slug' => 'web',
            'status' => 'enabled'
        ]);

        $this->insert('publicity_shape', [
            'name' => 'Otro cliente',
            'slug' => 'other_customer',
            'status' => 'enabled'
        ]);

        $this->insert('publicity_shape', [
            'name' => 'Facebook',
            'slug' => 'facebook',
            'status' => 'enabled'
        ]);

        $this->insert('publicity_shape', [
            'name' => 'Pasacalle',
            'slug' => 'street_banner',
            'status' => 'enabled'
        ]);

        $this->insert('publicity_shape', [
            'name' => 'Vendedor Puerta a puerta',
            'slug' => 'door_to_door',
            'status' => 'enabled'
        ]);

        $this->insert('publicity_shape', [
            'name' => 'Competencia',
            'slug' => 'competition',
            'status' => 'enabled'
        ]);

        $this->insert('publicity_shape', [
            'name' => 'Instagram',
            'slug' => 'instagram',
            'status' => 'enabled'
        ]);

        $this->insert('publicity_shape', [
            'name' => 'Gigantografía',
            'slug' => 'gigantografía',
            'status' => 'enabled'
        ]);

        $this->insert('publicity_shape', [
            'name' => 'Pantalla led',
            'slug' => 'pantalla-led',
            'status' => 'enabled'
        ]);

        $this->insert('publicity_shape', [
            'name' => 'Folleto',
            'slug' => 'folleto',
            'status' => 'enabled'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('publicity_shape');
    }
}
