<?php

use app\modules\config\models\Category;
use app\modules\config\models\Config;
use app\modules\config\models\Item;
use yii\db\Migration;

/**
 * Class m190524_151046_infobip_config_params
 */
class m190524_151046_infobip_config_params extends Migration
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
        $category = Category::findOne(['name' => 'Infobip']);

        if (empty($category)) {
            $category = new Category(['name' => 'Infobip', 'status' => 'enabled']);
            $category->save();
        }

        $this->insert('item', [
            'attr' => 'infobip_base_url',
            'type' => 'textInput',
            'label' =>"Url base de la api de Infobip",
            'description' => "",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 'https://8l5pe.api.infobip.com/sms/2'
        ]);

        $this->insert('item', [
            'attr' => 'infobip_user',
            'type' => 'textInput',
            'label' =>"Usuario de Infobip",
            'description' => "",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => '1Westnet!'
        ]);

        $this->insert('item', [
            'attr' => 'infobip_pass',
            'type' => 'textInput',
            'label' =>"Contraseña de Infobip",
            'description' => "",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => '2Westnet!'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'infobip_base_url'])->one();

        $configs = Config::find()->where(['item_id'])->all();

        foreach ($configs as $config) {
            $config->delete();
        }

        $item->delete();

        $item = Item::find()->where(['attr' => 'infobip_user'])->one();

        $configs = Config::find()->where(['item_id'])->all();

        foreach ($configs as $config) {
            $config->delete();
        }

        $item->delete();

        $item = Item::find()->where(['attr' => 'infobip_pass'])->one();

        $configs = Config::find()->where(['item_id'])->all();

        foreach ($configs as $config) {
            $config->delete();
        }

        $item->delete();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190524_151046_infobip_config_params cannot be reverted.\n";

        return false;
    }
    */
}
