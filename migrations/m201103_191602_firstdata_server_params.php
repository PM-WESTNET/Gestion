<?php

use yii\db\Migration;
use app\modules\config\models\Category;

/**
 * Class m201103_191602_firstdata_server_params
 */
class m201103_191602_firstdata_server_params extends Migration
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
        $category = Category::findOne(['name' => 'Firstdata']);

        if (empty($category)) {
            $category = new Category([
                'name' => 'Firstdata',
                'status' => 'enabled'
            ]);

            $category->save();
        }

        $this->insert('item', [
            'attr' => 'firstdata_server_url',
            'type' => 'textInput',
            'label' => "Url servidor de datos para firstdata",
            'description' => "",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => ''
        ]);

        $this->insert('item', [
            'attr' => 'firstdata_server_port',
            'type' => 'textInput',
            'label' => "Puerto del server de firstdata",
            'description' => "",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => ''
        ]);

        $this->insert('item', [
            'attr' => 'firstdata_api_token',
            'type' => 'textInput',
            'label' => "Token de la api de firstdata",
            'description' => "",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => ''
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201103_191602_firstdata_server_params cannot be reverted.\n";

        return false;
    }
    */
}
