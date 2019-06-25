<?php
use yii\db\Migration;
use app\modules\config\models\Category;
use app\modules\config\models\Item;
use app\modules\config\models\Config;

class m190408_124343_add_config_for_notification_replacement_length extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Notificaciones']);

        $this->insert('item', [
            'attr' => 'notification-replace-@Nombre',
            'type' => 'textInput',
            'label' =>"Indica cantidad de caracteres @Nombre",
            'description' => "Indica cada cuántos caracteres va a significar este campo al momento del reemplazo",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 20
        ]);

        $this->insert('item', [
            'attr' => 'notification-replace-@Telefono1',
            'type' => 'textInput',
            'label' =>"Indica cantidad de caracteres @Telefono1",
            'description' => "Indica cada cuántos caracteres va a significar este campo al momento del reemplazo",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 13
        ]);

        $this->insert('item', [
            'attr' => 'notification-replace-@Telefono2',
            'type' => 'textInput',
            'label' =>"Indica cantidad de caracteres @Telefono2",
            'description' => "Indica cada cuántos caracteres va a significar este campo al momento del reemplazo",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 13
        ]);

        $this->insert('item', [
            'attr' => 'notification-replace-@Codigo',
            'type' => 'textInput',
            'label' =>"Indica cantidad de caracteres @Codigo",
            'description' => "Indica cada cuántos caracteres va a significar este campo al momento del reemplazo",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 14
        ]);

        $this->insert('item', [
            'attr' => 'notification-replace-@CodigoDePago',
            'type' => 'textInput',
            'label' =>"Indica cantidad de caracteres",
            'description' => "Indica cada cuántos caracteres va a significar este campo al momento del reemplazo",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 14
        ]);

        $this->insert('item', [
            'attr' => 'notification-replace-@CodigoEmpresa',
            'type' => 'textInput',
            'label' =>"Indica cantidad de caracteres @CodigoEmpresa",
            'description' => "Indica cada cuántos caracteres va a significar este campo al momento del reemplazo",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 4
        ]);

        $this->insert('item', [
            'attr' => 'notification-replace-@FacturasAdeudadas',
            'type' => 'textInput',
            'label' =>"Indica cantidad de caracteres @FacturasAdeudadas",
            'description' => "Indica cada cuántos caracteres va a significar este campo al momento del reemplazo",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 2
        ]);

        $this->insert('item', [
            'attr' => 'notification-replace-@Saldo',
            'type' => 'textInput',
            'label' =>"Indica cantidad de caracteres @Saldo",
            'description' => "Indica cada cuántos caracteres va a significar este campo al momento del reemplazo",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 8
        ]);

        $this->insert('item', [
            'attr' => 'notification-replace-@Categoria',
            'type' => 'textInput',
            'label' =>"Indica cantidad de caracteres @Categoria",
            'description' => "Indica cada cuántos caracteres va a significar este campo al momento del reemplazo",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 10
        ]);

        $this->insert('item', [
            'attr' => 'notification-replace-@Nodo',
            'type' => 'textInput',
            'label' =>"Indica cantidad de caracteres @Nodo",
            'description' => "Indica cada cuántos caracteres va a significar este campo al momento del reemplazo",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 10
        ]);
    }

    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'notification-replace-@Nombre'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'notification-replace-@Telefono1'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'notification-replace-@Telefono2'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'notification-replace-@CodigoDePago'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'notification-replace-@CodigoEmpresa'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'notification-replace-@FacturasAdeudadas'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'notification-replace-@Saldo'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'notification-replace-@Categoria'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'notification-replace-@Codigo'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'notification-replace-@Nodo'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

    }
}