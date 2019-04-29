<?php

use yii\db\Schema;
use yii\db\Migration;

class m151201_121568_ecopago_config extends Migration {

    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp() {

        $ecopagoCategoryId = 0;

        //Category (Agenda insert)
        if ($this->db->schema->getTableSchema('category') !== null) {

            $this->insert('category', [
                'name' => 'Ecopago',
                'status' => 'enabled',
            ]);

            $ecopagoCategoryId = $this->db->getLastInsertID();
        }

        //Category (Agenda insert)
        if ($this->db->schema->getTableSchema('item') !== null && $ecopagoCategoryId > 0) {

            $this->insertItem([
                'attr' => 'payment_method',
                'type' => 'textInput',
                'default' => 'Contado',
                'label' => 'Método de pago',
                'description' => 'Método de pago utilizado por defecto para pagos de Ecopagos',
                'multiple' => false,
                'category_id' => $ecopagoCategoryId,
                'superadmin' => true,
                    ], [
                [
                    'validator' => 'string',
                ],
                [
                    'validator' => 'required',
                ]
                    ]
            );
            $this->insertItem([
                'attr' => 'ecopago_money_box_id',
                'type' => 'textInput',
                'default' => '',
                'label' => 'Id entidad monetaria recaudadora',
                'description' => 'Entidad monetaria utilizada para mostrar a que entidades bancarias rendir dinero de cierres de lote',
                'multiple' => false,
                'category_id' => $ecopagoCategoryId,
                'superadmin' => true,
                    ], [
                [
                    'validator' => 'string',
                ],
                [
                    'validator' => 'required',
                ]
                    ]
            );
            $this->insertItem([
                'attr' => 'chrome_print_app',
                'type' => 'textInput',
                'default' => 'idlipbhoabgfdjkbpicgjjidfmgfcnmj',
                'label' => 'ID App para Google Chrome (Manejador de impresiones WestNet)',
                'description' => 'ID de la app para Google Chrome que se utiliza para realizar las impresiones en las ticketeras. Es necesario que este ID sea valido y sea el mismo que provee la instalacion de la app en el explorador Chrome (en vista de Extensiones este ID es visible).',
                'multiple' => false,
                'category_id' => $ecopagoCategoryId,
                'superadmin' => true,
                    ], [
                [
                    'validator' => 'string',
                ],
                [
                    'validator' => 'required',
                ]
                    ]
            );
        }
    }

    /**
     * @brief Inserta un item con sus validadores
     * @param type $attributes
     * @param type $rules
     */
    private function insertItem($attributes = [], $rules = []) {

        $this->insert('item', $attributes);
        $itemId = $this->db->getLastInsertID();

        if ($itemId > 0) {

            if (!empty($rules))
                foreach ($rules as $rule)
                    $this->insertRule($rule, $itemId);

            //Insertamos config
            $this->insertConfig([
                'value' => $attributes['default']
                    ], $itemId);
        }
    }

    /**
     * @brief Inserta reglas pára un item determinado
     * @param type $rule
     * @param type $itemId
     */
    private function insertRule($rule = [], $itemId = 0) {

        if (!empty($rule) && $itemId > 0) {

            $rule['item_id'] = $itemId;

            $this->insert('rule', $rule);
        }
    }

    /**
     * @brief Inserta una configuracion segun un item
     * @param array $attributes
     * @param type $itemId
     */
    private function insertConfig($config = [], $itemId = 0) {

        if (!empty($config) && $itemId > 0) {
            $config['item_id'] = $itemId;

            $this->insert('config', $config);
        }
    }

    public function safeDown() {
        
    }

}
