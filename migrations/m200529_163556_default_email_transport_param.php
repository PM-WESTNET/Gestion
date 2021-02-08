<?php

use yii\db\Migration;
use app\modules\config\models\Item;

/**
 * Class m200529_163556_default_email_transport_param
 */
class m200529_163556_default_email_transport_param extends Migration
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
        $category = \app\modules\config\models\Category::findOne(['name' => 'General']);

        $this->insert('item', [
            'attr' => 'defaultEmailTransport',
            'type' => 'textInput',
            'label' => "Email Tranport por defecto ",
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 4
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $item = Item::findOne(['attr' => 'defaultEmailTransport']);

        if($item) {
            $item->delete();
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200529_163556_default_email_transport_param cannot be reverted.\n";

        return false;
    }
    */
}
