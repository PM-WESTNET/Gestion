<?php

use yii\db\Migration;

/**
 * Class m200204_111717_change_data_type_from_publicity_shape_field_in_customer_table
 */
class m200204_111717_change_data_type_from_publicity_shape_field_in_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('customer', 'publicity_shape', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('customer', 'publicity_shape', "ENUM('banner', 'poster', 'web', 'other_customer', 'facebook', 'street_banner', 'magazine', 'door_to_door', 'competition', 'brochure', 'gigantografia', 'pantalla-led', 'instagram')");
    }
}
