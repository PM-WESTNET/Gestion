<?php

use yii\db\Migration;
use app\modules\westnet\models\Vendor;
use app\modules\sale\models\Company;

class m181204_152323_modify_field_model_into_log_table extends Migration
{

    public function init(){
        $this->db = 'dblog';
        parent::init();
    }
    public function up()
    {
        $this->alterColumn('log', 'model', $this->text(100));
    }

    public function down()
    {
        $this->alterColumn('log', 'model', $this->text(45));
    }
}
