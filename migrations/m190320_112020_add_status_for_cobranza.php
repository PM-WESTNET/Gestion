<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 20/03/19
 * Time: 11:20
 */

use yii\db\Migration;
use app\modules\ticket\components\schemas\SchemaCobranza;
use app\modules\ticket\models\Schema;
use app\modules\ticket\models\Status;

class m190320_112020_add_status_for_cobranza extends Migration
{
    const STATUSES = [
            [
                'name' => 'Compromiso de pago',
                'description' => 'Se compromete a realizar el pago',
                'is_open' => 1,
            ],
            [
                'name' => 'Extensión',
                'description' => 'Extensión de pago',
                'is_open' => 1,
            ],
            [
                'name' => 'Informado',
                'description' => 'Ha sido informado',
                'is_open' => 1,
            ],
            [
                'name' => 'No va a pagar',
                'description' => 'Informa que no va a pagar',
                'is_open' => 0,
            ],
            [
                'name' => 'Plan de pago',
                'description' => 'Se realiza plan de pago',
                'is_open' => 0,
            ],
            [
                'name' => 'Problemas técnicos',
                'description' => 'Presenta problemas técnicos',
                'is_open' => 1,
            ],
            [
                'name' => 'Pago parcial',
                'description' => 'Se realiza un pago parcial',
                'is_open' => 0,
            ],
            [
                'name' => 'Tel erróneo sin comunicación',
                'description' => 'No es posible la comunicación',
                'is_open' => 0,
            ],
            [
                'name' => 'Pagó',
                'description' => 'Informa un pago realizado',
                'is_open' => 0,
            ],
            [
                'name' => 'Pago 1 de 2',
                'description' => 'Informa primer cuota pagada de dos cuotas totales',
                'is_open' => 0,
            ],
        ];

    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {

        $this->insert('schema', [
            'name' => 'Cobranza',
            'class' => SchemaCobranza::class
        ]);

        $schema_cobranza = Schema::find()->where(['name' => 'Cobranza'])->one();

        foreach (self::STATUSES as $status) {
            $this->insert('status', [
                'name' => $status['name'],
                'description' => $status['description'],
                'is_open' => $status['is_open']
            ]);

            $status_id = $this->getDb()->lastInsertID;

            $this->insert('schema_has_status', [
                'schema_id' => $schema_cobranza->schema_id,
                'status_id' => $status_id
            ]);
        }

        $status_nuevo = Status::find()->where(['name' => 'nuevo'])->one();

        $this->insert('schema_has_status', [
            'schema_id' => $schema_cobranza->schema_id,
            'status_id' => $status_nuevo->status_id
        ]);
    }

    public function safeDown()
    {
        $schema_cobranza = Schema::find()->where(['name' => 'Cobranza'])->one();
        $schema_cobranza->unlinkAll('statuses', true);
        $status_nuevo = Status::find()->where(['name' => 'nuevo'])->one();

        foreach (self::STATUSES as $status) {
            $this->delete('status', [
                'name' => $status['name']
            ]);
        }

        $this->delete('schema_has_status', [
            'schema_id' => $schema_cobranza->schema_id,
            'status_id' => $status_nuevo->status_id
        ]);
    }
}