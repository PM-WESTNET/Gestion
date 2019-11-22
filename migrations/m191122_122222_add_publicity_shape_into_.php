<?php

use yii\db\Migration;

class m191122_122222_add_publicity_shape_into_ extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('customer', 'publicity_shape', "ENUM('banner', 'poster', 'web', 'other_customer', 'facebook', 'street_banner', 'magazine', 'door_to_door', 'competition', 'brochure', 'gigantografía', 'pantalla-led', 'instagram')");

    }

    public function safeDown()
    {
        $this->alterColumn('customer', 'publicity_shape', "ENUM('banner', 'poster', 'web', 'other_customer', 'facebook', 'street_banner', 'magazine', 'door_to_door', 'competition', 'brochure')");

    }
}
