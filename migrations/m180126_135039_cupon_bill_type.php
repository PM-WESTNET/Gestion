<?php

use app\modules\config\models\Category;
use yii\db\Migration;

/**
 * Class m180126_135039_cupon_bill_type
 */
class m180126_135039_cupon_bill_type extends Migration
{

    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Comprobantes']);

        $this->insert('item', [
            'attr' => 'cupon_bill_types',
            'type' => 'textInput',
            'label' => 'Ids de Comprobantes que imprime Cupon',
            'description' => 'Ids de Comprobantes que imprime Cupon',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 6
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180126_135039_cupon_bill_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180126_135039_cupon_bill_type cannot be reverted.\n";

        return false;
    }
    */
}
