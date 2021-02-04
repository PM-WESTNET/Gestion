<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_204234_insert_agenda_values extends Migration {

    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp() {

        $agendaCategoryId = 0;

        //Category (Agenda insert)
        if ($this->db->schema->getTableSchema('category') !== null) {

            $this->insert('category', [
                'name' => 'Agenda',
                'status' => 'enabled',
            ]);

            $agendaCategoryId = $this->db->getLastInsertID();
        }

        //Category (Agenda insert)
        if ($this->db->schema->getTableSchema('item') !== null && $agendaCategoryId > 0) {

            $this->insertItem([
                'attr' => 'check_expiration_on_login',
                'type' => 'checkbox',
                'default' => true,
                'label' => 'Revisar tareas vencidas al iniciar sesión',
                'description' => 'Indica si se revisarán las tareas vencidas de un usuario cuando loguee o no.',
                'multiple' => false,
                'category_id' => $agendaCategoryId,
                'superadmin' => true,
                    ], [
                [
                    'validator' => 'boolean',
                ],
                [
                    'validator' => 'required',
                ]
                    ]
            );
            $this->insertItem([
                'attr' => 'check_expiration_timeout',
                'type' => 'textInput',
                'default' => 28800,
                'label' => 'Timeout para revisión de tareas vencidas (s)',
                'description' => 'Timeout para revisión de tareas vencidas (en segundos): 28800s por defecto',
                'multiple' => false,
                'category_id' => $agendaCategoryId,
                'superadmin' => true,
                    ], [
                [
                    'max' => '172800',
                    'min' => '60',
                    'validator' => 'number',
                ],
                [
                    'validator' => 'required',
                ]
                    ]
            );
            $this->insertItem([
                'attr' => 'work_hours_start',
                'type' => 'textInput',
                'default' => '08:00',
                'label' => 'Hora de inicio de día laboral',
                'description' => 'Indica la hora de inicio de un día laboral (formato H:i)',
                'multiple' => false,
                'category_id' => $agendaCategoryId,
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
                'attr' => 'work_hours_end',
                'type' => 'textInput',
                'default' => '18:00',
                'label' => 'Hora de fin de día laboral',
                'description' => 'Indica la hora de fin de un día laboral (formato H:i)',
                'multiple' => false,
                'category_id' => $agendaCategoryId,
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
                'attr' => 'work_hours_quantity',
                'type' => 'textInput',
                'default' => '10:00',
                'label' => 'Cantidad de horas laborables en un día',
                'description' => 'Cantidad de horas laborables en un día habil (Formato H:i, i.e. 10 horas laborables => 10:00)',
                'multiple' => false,
                'category_id' => $agendaCategoryId,
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
