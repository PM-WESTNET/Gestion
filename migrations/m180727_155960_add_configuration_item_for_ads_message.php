<?php

use yii\db\Migration;

class m180727_155960_add_configuration_item_for_ads_message extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {
        $this->execute('ALTER TABLE item MODIFY `default` text;');
        $this->insert('category', [
            'name' => 'ADS',
            'status' => 'enabled',
            'superadmin' => null
        ]);
        
        $categoryId = $this->db->getLastInsertID();
        
        $this->insert('item', [
            'attr' => 'ads-title',
            'type' => 'textInput',
            'default' => 'SR CLIENTE LEA ATENTAMENTE LOS SIGUIENTE PUNTOS:',
            'label' => 'Título',
            'description' => 'Título de mensaje en ADS - Para reemplazar el nombre de la Empresa coloque @Empresa',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0
        ]);
        
        $this->insert('item', [
            'attr' => 'ads-message',
            'type' => 'textInput',
            'default' => '  <p>1. La Empresa no realiza per&iacute;odo de prueba alguno, una vez instalado es asumido el compromiso de pago.<br />
                            2. Plazo m&iacute;nimo de contrato: 6 meses..<br />
                            3. Los equipamientos instalados (antena, radio, fuente, cables) son en comodato (propiedad de la Empresa @Empresa).<br />
                            4. Plazo de pago de la primer cuota monto a definir por administraci&oacute;n: de 24 hs..<br />
                            5. El ancho de banda m&iacute;nimo garantizado (Mbps m&iacute;nima ofrecida) se encuentra detallado en el presente documento.<br />
                            6. El primer pago debe realizarse como m&aacute;ximo 24 hs despues de realizada la instalacion..<br />
                            7. En caso de adquirir router WiFi, el mismo tiene un costo adicional y posee una garant&iacute;a de 3 meses desde el d&iacute;a que se instala.</p>',
            'label' => 'Mensaje',
            'description' => 'Mensaje en ADS',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0
        ]);
    }

    public function down()
    {
        $this->delete('category', [
            'name' => 'ADS'
        ]);
        
        $this->delete('item',[
            'attr' => 'ads-message'
        ]);
    }

}
