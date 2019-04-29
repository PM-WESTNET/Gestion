<?php

use yii\db\Migration;

class m180827_122118_sanatize_provider_bill extends Migration
{
    public function up()
    {
        $this->db->createCommand("UPDATE provider_bill 
                                    SET number = CASE
                                    WHEN LENGTH(REPLACE(number, '-', '')) = 12 
                                        THEN CONCAT(LEFT(REPLACE(number, '-', ''),4),'-',RIGHT(REPLACE(number, '-', ''),8))
                                    WHEN LENGTH(REPLACE(number, '-', '')) < 12 
                                        THEN CONCAT(LEFT(LPAD(REPLACE(number, '-', ''),12,'0'),4),'-',RIGHT(LPAD(REPLACE(number, '-', ''),12,'0'),8))
                                    WHEN LENGTH(REPLACE(number, '-', '')) > 12 
                                        THEN CONCAT(LEFT(RIGHT(REPLACE(number, '-', ''),12),4),'-',RIGHT(RIGHT(REPLACE(number, '-', ''),12),8))
                                    WHEN number IS NULL 
                                        THEN CONCAT(LPAD('', 4, 0), '-', LPAD('', 8, 0)) 
                                   END
        ")->execute();
    }

    public function down()
    {

    }

}
