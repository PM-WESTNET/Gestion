<?php

use yii\db\Schema;
use yii\db\Migration;

class m180809_095240_modify_log_table extends Migration
{

    public function init()
    {
        $this->db = 'dblog';
        parent::init();
    }

    public function up()
    {
        $this->addColumn('log', 'attribute', $this->string());
        $this->addColumn('log', 'old_value', $this->string());
        $this->addColumn('log', 'new_value', $this->string());
        $this->addColumn('log', 'post', $this->string());
        $this->renameColumn('log', 'data', 'get');
    }

    public function down()
    {
        $this->renameColumn('log', 'get','data');
        $this->dropColumn('log', 'post');
        $this->dropColumn('log', 'new_value');
        $this->dropColumn('log', 'old_value');
        $this->dropColumn('log', 'attribute');
    }

}
