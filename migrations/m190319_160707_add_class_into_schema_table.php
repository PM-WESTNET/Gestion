<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 19/03/19
 * Time: 16:07
 */

use yii\db\Migration;
use app\modules\ticket\models\Category;
use app\modules\ticket\models\Schema;

class m190319_160707_add_class_into_schema_table extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('schema', 'class', $this->string());

        $this->update('schema', ['class' => 'app\modules\ticket\components\schemas\SchemaDefault']);

        $schema = Schema::findOne(['name' => 'Default']);

        $this->update('category', [
            'schema_id' => $schema->schema_id
        ]);
    }

    public function safeDown()
    {
        $this->update('category', ['schema_id' => null]);

        $this->dropColumn('schema', 'class');
    }
}