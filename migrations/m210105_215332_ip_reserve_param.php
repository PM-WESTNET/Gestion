<?php

use yii\db\Migration;
use app\modules\config\models\Category;

/**
 * Class m210105_215332_ip_reserve_param
 */
class m210105_215332_ip_reserve_param extends Migration
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
        $category = \app\modules\config\models\Category::findOne(['name' => 'Westnet']);

        $this->insert('item', [
            'attr' => 'ip_reserve_count',
            'type' => 'textInput',
            'label' => "Cantidad de ips resevadas por rango",
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 20
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210105_215332_ip_reserve_param cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210105_215332_ip_reserve_param cannot be reverted.\n";

        return false;
    }
    */
}
