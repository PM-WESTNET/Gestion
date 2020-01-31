<?php

use yii\db\Migration;

/**
 * Class m191212_205425_aws_max_send_rate_config
 */
class m191212_205425_aws_max_send_rate_config extends Migration
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
        $category = \app\modules\config\models\Category::findOne(['name' => 'Notificaciones']);

        if(empty($category)) {
            $category = new \app\modules\config\models\Category([
                'name' => 'Notificaciones',
                'status' => 'enabled'
            ]);

            $category->save();
        }

        $this->insert('item', [
            'attr' => 'aws_max_send_rate',
            'type' => 'textInput',
            'label' =>"Cuota máxima de mensajes por segundo de AWS",
            'description' => "Cantidad de mensajes que admite Amazon SES por segundo",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 28
        ]);


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $item = \app\modules\config\models\Item::findOne(['attr' => 'aws_max_send_rate']);

        if ($item) {
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
        echo "m191212_205425_aws_max_send_rate_config cannot be reverted.\n";

        return false;
    }
    */
}
