<?php

use app\modules\accounting\models\MoneyBoxType;
use yii\db\Schema;
use yii\db\Migration;

class m160229_163105_bancos extends Migration
{

    private function addMoneyBoxes($money_box_type_id, $names)
    {
        foreach($names as $key=>$value) {
            $this->insert('money_box', [
                'name'              => $value,
                'money_box_type_id' => $money_box_type_id
            ]);
        }
    }

    public function up()
    {

        $this->insert('money_box_type', [
            'name'  => 'Bancaria',
            'code'  => 'BANCARIA'
        ]);

        $money_box_type_id = Yii::$app->db->lastInsertID;
        $this->addMoneyBoxes($money_box_type_id,
            ['ABN AMRO',
            'American Express Bank',
            'BACS',
            'Banco B.I. Creditanstalt',
            'Banco Bradesco',
            'Banco Cetelem',
            'Banco Ciudad',
            'Banco CMF',
            'Banco Cofidis',
            'Banco Columbia',
            'Banco Comafi',
            'Banco Credicoop',
            'Banco de Córdoba',
            'Banco de Corrientes',
            'Banco de Formosa',
            'Banco de La Pampa',
            'Banco de San Juan',
            'Banco de Santiago del Estero',
            'Banco de Servicios Financieros',
            'Banco de Servicios y Transacciones',
            'Banco de Tierra del Fuego',
            'Banco de Valores',
            'Banco del Chubut',
            'Banco del Sol',
            'Banco del Tucumán',
            'Banco del Uruguay',
            'Banco do Brasil',
            'Banco Finansur',
            'Banco Galicia',
            'Banco Hipotecario',
            'Banco Industrial',
            'Banco Itaú',
            'Banco Julio',
            'Banco Macro',
            'Banco Mariva',
            'Banco Masventas',
            'Banco Meridian',
            'Banco Municipal de Rosario',
            'Banco Nación',
            'Banco Patagonia',
            'Banco Piano',
            'Banco Provincia',
            'Banco Provincia del Neuquén',
            'Banco Regional de Cuyo',
            'Banco Roela',
            'Banco Saenz',
            'Banco Santa Cruz',
            'Banco Santander Río',
            'Banco Supervielle',
            'Bank of America',
            'Bank of Tokyo-Mitsubishi UFJ',
            'BBVA Banco Francés',
            'BICE',
            'BNP Paribas',
            'Citibank',
            'Deutsche Bank',
            'HSBC Bank',
            'ICBC',
            'JPMorgan',
            'MBA Lazard Banco De Inversiones',
            'Nuevo Banco de Entre Ríos',
            'Nuevo Banco de La Rioja',
            'Nuevo Banco de Santa Fe',
            'Nuevo Banco del Chaco',
            'RCI Banque']);
        echo "------------------------------------------------------------------------------"."\n";
        echo "En caso de necesitarlo modifique las cuentas contables asociadas a cada banco."."\n";
        echo "------------------------------------------------------------------------------"."\n";
    }

    public function down()
    {
        echo "m160229_163105_bancos cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
