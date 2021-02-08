<?php

use yii\db\Migration;

class m160608_195958_afip_certificates_in_relative_path extends Migration
{
    public function up()
    {
        $this->execute("UPDATE `company` SET"
                . "`company`.`certificate` = SUBSTRING( `company`.`certificate` FROM POSITION( 'uploads' IN `company`.`certificate` ) ),"
                . "`company`.`key` = SUBSTRING( `company`.`key` FROM POSITION( 'uploads' IN `company`.`key` ) )");
    }

    public function down()
    {
        echo "m160608_195958_afip_certificates_in_relative_path cannot be reverted.\n";

        return false;
    }
}
