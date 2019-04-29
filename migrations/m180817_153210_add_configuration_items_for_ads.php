<?php

use yii\db\Migration;

class m180817_153210_add_configuration_items_for_ads extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $category = \app\modules\config\models\Category::findOne(['name' => 'ADS']);
        $text_item = \app\modules\config\models\Item::findOne(['attr' => 'ads-message']);

        $this->insert('item', [
            'attr' => 'ads-contact_technical_service',
            'type' => 'textInput',
            'label' => 'Teléfonos de contacto del servicio técnico',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 'TEL: 02614200997 OPCIÓN 3 / 2615087213 '
        ]);

        $this->insert('item', [
            'attr' => 'ads-time_technical_service',
            'type' => 'textInput',
            'label' => 'Horarios de atención del servicio técnico',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 'LUN. A VIE. 09:00 A 17:00 HS – SAB 09:00 A 13:00 HS'
        ]);
        
        $this->insert('item', [
            'attr' => 'ads-comercial-office',
            'type' => 'textInput',
            'label' => 'Datos de oficina comercial',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 'TEL. 0261 4 200997 OPCIÓN 1 / 261 6547474'
        ]);
        
        $this->insert('item', [
            'attr' => 'ads-email_technical_service',
            'type' => 'textInput',
            'label' => 'Mail del servicio técnico',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 'soporte@westnet.com.ar'
        ]);
        
        $this->db->createCommand("UPDATE config SET value = '1). La empresa No realiza ensayos, una vez instalado el servicio de Internet se asume el compromiso de pago.<br>
                                                             2). El Plazo m&iacute;nimo del contrato es de 6 meses.<br>
                                                             3). Los equipos instalados (Antena, radio, fuente, cables y Router) Son en <u><strong>COMODATO</strong></u>, y<strong> </strong>son propiedad de la Empresa <u>Westnet</u>.<br>
                                                             4). El ancho de banda m&iacute;nimo garantizado (Mbps m&iacute;nima ofrecida) se encuentra detallado en el presente documento.<br>
                                                             5). El Importe del primer comprobante es informado con antelaci&oacute;n por el sector de Administraci&oacute;n.<br>
                                                             6). El plazo m&aacute;ximo para realizar el primer pago es de 24 Hs. Posterior a realizada la Instalaci&oacute;n.<br>
                                                             7). En caso de adquirir un Router adicional al de la instalaci&oacute;n, el cliente tiene que abonar por &uacute;nica vez el valor del mismo.<br>
                                ' WHERE item_id  = $text_item->item_id")->execute();
    }

    public function down()
    {
     
    }
}
