<?php

use yii\db\Migration;

/**
 * Class m180912_143001_consumidor_final
 */
class m180912_143001_consumidor_final extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO profile_class (name, data_type, status, hint, searchable, data_min, data_max, pattern, `order`) VALUES ('Consumidor Final', 'checkbox', 'enabled', '', 0, '', NULL, '', 1);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180912_143001_consumidor_final cannot be reverted.\n";

        return false;
    }
}
