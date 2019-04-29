<?php

use app\modules\config\models\Category;
use yii\db\Migration;

/**
 * Class m180712_124616_companies_without_bills
 */
class m180712_124616_companies_without_bills extends Migration
{

    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }


    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Comprobantes']);

        $this->insert('item', [
            'attr' => 'companies_without_bills',
            'type' => 'textInput',
            'label' => 'Ids de Empresas sin Facturación',
            'description' => 'Ids de Empresas sin Facturación',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => ''
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180712_124616_companies_without_bills cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180712_124616_companies_without_bills cannot be reverted.\n";

        return false;
    }
    */
}
