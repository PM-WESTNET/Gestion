<?php

use app\modules\checkout\models\PagoFacilTransmitionFile;
use yii\db\Migration;

class m191209_151212_add_status_pending_into_pago_facil_transmition_file_table extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('pago_facil_transmition_file', 'status', "ENUM('draft', 'closed', 'pending')");
    }

    public function safeDown()
    {
        foreach (PagoFacilTransmitionFile::find()->all() as $file) {
            if($file->status == PagoFacilTransmitionFile::STATUS_PENDING) {
                $file->updateAttributes(['status' => PagoFacilTransmitionFile::STATUS_DRAFT]);
            }
        }

        $this->alterColumn('pago_facil_transmition_file', 'status', "ENUM('draft', 'closed')");
    }
}
