<?php

use yii\db\Migration;

class m180830_110318_add_electronic_billing_field_into_point_of_sale extends Migration
{
    public function up()
    {
        $this->addColumn('point_of_sale', 'electronic_billing', $this->boolean());
        $this->update('point_of_sale',
            ['electronic_billing' => true ],
            ['<>','description', 'Punto de venta por migración.']);
        $this->update('point_of_sale',
            ['electronic_billing' => false ],
            ['description' => 'Punto de venta por migración.']);
    }

    public function down()
    {
        $this->dropColumn('point_of_sale', 'electronic_billing');
    }

}
