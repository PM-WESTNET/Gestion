<?php

use yii\db\Migration;

/**
 * Class m200220_145956_tiempo_extension_de_pago_param
 */
class m200220_145956_tiempo_extension_de_pago_param extends Migration
{

    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $category = \app\modules\config\models\Category::findOne(['name' => 'Mobile App']);

        $this->insert('item', [
            'attr' => 'time_between_payment_extension',
            'type' => 'textInput',
            'label' => "Tiempo mínimo entre extensiones de pago",
            'description' => "En minutos. Tiempo mínimo entre 2 extensiones de pago",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 10
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $item = \app\modules\config\models\Item::findOne(['attr' => 'time_between_payment_extension']);

        if ($item) {
            return $item->delete();
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200220_145956_tiempo_extension_de_pago_param cannot be reverted.\n";

        return false;
    }
    */
}
