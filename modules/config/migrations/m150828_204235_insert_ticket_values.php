<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_204235_insert_ticket_values extends Migration {

    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp() {

        $ticketCategoryId = 0;

        //Category (Agenda insert)
        if ($this->db->schema->getTableSchema('category') !== null) {

            $this->insert('category', [
                'name' => 'Ticket',
                'status' => 'enabled',
            ]);

            $ticketCategoryId = $this->db->getLastInsertID();
        }

        //Category (Ticket insert)
        if ($this->db->schema->getTableSchema('item') !== null && $ticketCategoryId > 0) {

            $this->insertItem([
                'attr' => 'expiration_timeout',
                'type' => 'textInput',
                'default' => 10,
                'label' => 'Timeout para cierre automático de tickets',
                'description' => 'Timeout para cerrar automáticamente los tickets abiertos (en días): 10 días por defecto',
                'multiple' => false,
                'category_id' => $ticketCategoryId,
                'superadmin' => true,
                    ], [
                [
                    'max' => '100',
                    'min' => '1',
                    'validator' => 'number',
                ],
                [
                    'validator' => 'required',
                ]
                    ]
            );
            $this->insertItem([
                'attr' => 'pagination_limit',
                'type' => 'textInput',
                'default' => 5,
                'label' => 'Límite de elementos para cada página de Ticket',
                'description' => 'Setea el límite que las páginas utilizadas en Ticket (principalmente listado de observaciones) muestran',
                'multiple' => false,
                'category_id' => $ticketCategoryId,
                'superadmin' => true,
                    ], [
                [
                    'max' => '100',
                    'min' => '1',
                    'validator' => 'number',
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
