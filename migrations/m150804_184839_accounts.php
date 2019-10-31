<?php

use yii\db\Schema;
use yii\db\Migration;

class m150804_184839_accounts extends Migration
{
    public function up()
    {

        $this->insertAccount(
            ['ACTIVO', 0, '1', 1, 68, [
                ['ACTIVO CORRIENTE', 0, '1.1', 2, 45, [
                        ['DISPONIBILIDADES', 0, '1.1.1', 3, 16, [
                                ['Caja pesos', 1, '1.1.1.1', 4, 5, null],
                                ['Caja Moneda Extranjera', 1, '1.1.1.2', 6, 7, null],
                                ['Fondo fijo', 1, '1.1.1.3', 8, 9, null],
                                ['Banco misc', 1, '1.1.1.4', 10, 11, null],
                                ['Valores a depositar', 1, '1.1.1.5', 12, 13, null],
                            ]
                        ],
                        ['CUENTAS POR COBRAR', 0, '1.1.2', 17, 22, [
                                ['Deudores por ventas', 1, '1.1.2.1', 18, 19, null],
                                ['Deudores morosos', 1, '1.1.2.2', 20, 21, null],
                            ]
                        ],
                        ['OTROS CRÉDITOS', 0, '1.1.3', 23, 38, [
                                ['Retencion impuestos a las ganancias', 1, '1.1.3.1', 24, 25, null],
                                ['Anticipo impuestos a las ganancias', 1, '1.1.3.2', 26, 27, null],
                                ['IVA crédito fiscal 21%', 1, '1.1.3.3', 28, 29, null],
                                ['IVA crédito fiscal 10.5 %', 1, '1.1.3.4', 30, 31, null],
                                ['Retencion IVA', 1, '1.1.3.5', 32, 33, null],
                                ['Retencion ingresos brutos', 1, '1.1.3.6', 34, 35, null],
                                ['Saldo a favor ingresos a las ganancias', 1, '1.1.3.7', 36, 37, null]
                            ]
                        ],
                        ['BIENES DE CAMBIO', 0, '1.1.4', 39, 44, [
                                ['Equipos', 1, '1.1.4.1', 40, 41, null],
                                ['Equipos en Demo', 1, '1.1.4.2', 42, 43, null]
                            ]
                        ],
                    ]
                ],
                ['ACTIVO NO CORRIENTE', 0, '1.2', 46, 67, [
                        ['BIENES DE USO', 0, '1.2.1', 47, 56, [
                                ['Equipos computación', 1, '1.2.1.1', 48, 49, null],
                                ['Software', 1, '1.2.1.2', 50, 51, null],
                                ['Instalaciones', 1, '1.2.1.3', 52, 53, null],
                                ['Muebles y útiles', 1, '1.2.1.4', 54, 55, null]
                            ]
                        ],
                        ['AMORTIZACIONES ACUMULADAS BIENES DE USO', 0, '1.2.2', 57, 66, [
                                ['Amortización acumulada equipos computación', 1, '1.2.2.1', 58, 59, null],
                                ['Amortización acumulada software', 1, '1.2.2.2', 60, 61, null],
                                ['Amortización acumulada instalaciones', 1, '1.2.2.3', 62, 63, null],
                                ['Amortización acumulada muebles y útiles', 1, '1.2.2.4', 64, 65, null]
                            ]
                        ]
                    ]
                ],
            ]
        ]);
        $this->insertAccount(
            ['PASIVO', 0, '2', 69, 120, [
                    ['PASIVO CORRIENTE', 0, '2.1', 70, 109, [
                            ['DEUDAS SOCIALES', 0, '2.1.1', 71, 84, [
                                    ['Sueldos a pagar', 1, '2.1.1.1', 72, 73, null],
                                    ['Cargas sociales a pagar', 1, '2.1.1.2', 74, 75, null],
                                    ['Provision SAC a pagar', 1, '2.1.1.3', 76, 77, 124],
                                    ['Provision SAC cargas sociales a pagar', 1, '2.1.1.4', 78, 79, null],
                                    ['Beneficios al personal', 1, '2.1.1.5', 80, 81, null],
                                    ['Previsión Despidos', 1, '2.1.1.6', 82, 83, null]
                                ]
                            ],
                            ['DEUDAS FISCALES', 0, '2.1.2', 85, 98, [
                                    ['Ingresos brutos a pagar', 1, '2.1.2.1', 86, 87, null],
                                    ['IVA débito fiscal 21%', 1, '2.1.2.2', 88, 89, null],
                                    ['IVA débito fiscal 10.5%', 1, '2.1.2.3', 90, 91, null],
                                    ['IVA a pagar', 1, '2.1.2.4', 92, 93, null],
                                    ['Retención ganancias a depositar', 1, '2.1.2.5', 94, 95, null],
                                    ['Provision impuesto a las ganancias', 1, '2.1.2.6', 96, 97, null]
                                ]
                            ],
                            ['DEUDAS COMERCIALES', 0, '2.1.3', 99, 104, [
                                    ['Proveedores', 1, '2.1.3.1', 100, 101, null],
                                    ['Anticipos de clientes', 1, '2.1.3.2', 102, 103, null]
                                ]
                            ],
                            ['PROVISIONES VARIAS', 0, '2.1.4', 105, 108, [
                                    ['Provision gastos', 1, '2.1.4.1', 106, 107, null],
                                ]
                            ],
                        ]
                    ],
                    ['PASIVO NO CORRIENTE', 0, '2.2', 110, 119, [
                            ['DEUDAS COMERCIALES A LARGO PLAZO', 0, '2.2.1', 111, 114, [
                                    ['Deudas proveedores a largo plazo', 1, '2.2.1.1', 112, 113, null],
                                ]
                            ],
                            ['OTRAS DEUDAS', 0, '2.2.2', 115, 118, [
                                    ['Deudas misc', 1, '2.2.2.1', 116, 117, null],
                                ]
                            ],
                        ]
                    ],
                ]
            ]);
        $this->insertAccount(
            ['PATRIMONIO NETO', 0, '3', 121, 132, [
                    ['Capital', 1, '3.1', 122, 123, null],
                    ['Aporte irrevocable ', 1, '3.2', 124, 125, null],
                    ['Reserva legal', 1, '3.3', 126, 127, null],
                    ['Resultados no asignados', 1, '3.4', 128, 129, null],
                    ['Resultado del ejercicio', 1, '3.5', 130, 131, null],
                ]
            ]);
        $this->insertAccount(
            ['INGRESOS', 0, '4', 133, 150, [
                    ['INGRESOS OPERATIVOS', 0, '4.1', 134, 145, [
                            ['VENTAS', 0, '4.1.1', 135, 142, [
                                    ['Ingresos por ventas', 1, '4.1.1.1', 136, 137, null],
                                    ['Venta de equipos', 1, '4.1.1.2', 138, 139, null],
                                    ['Venta servicios misc', 1, '4.1.1.3', 140, 141, null],
                                ]
                            ],
                            ['OTROS INGRESOS', 0, '4.1.2', 143, 144, null],
                        ]
                    ],
                    ['INGRESOS FINANCIEROS', 0, '4.2', 146, 149, [
                            ['Resultado de inversiones financieras', 1, '4.2.1', 147, 148, null],
                        ]
                    ]
                ]
            ]);
        $this->insertAccount(
            ['GASTOS', 0, '5', 151, 222, [
                ['GASTOS OPERATIVOS', 0, '5.1', 152, 209, [
                        ['GASTOS DE PRODUCCION', 0, '5.1.1', 153, 164, [
                                ['Gastos operativos general', 1, '5.1.1.1', 154, 155, null],
                                ['Haberes', 1, '5.1.1.2', 156, 157, null],
                                ['Cargas Sociales', 1, '5.1.1.3', 158, 159, null],
                                ['Costo de equipos', 1, '5.1.1.4', 160, 161, null],
                                ['Materiales', 1, '5.1.1.5', 162, 163, null],
                            ]
                        ],
                        ['GASTOS COMERCIALES', 0, '5.1.2', 165, 174, [
                                ['Haberes comerciales', 1, '5.1.2.1', 166, 167, null],
                                ['Cargas sociales comerciales', 1, '5.1.2.2', 168, 169, null],
                                ['Comisiones', 1, '5.1.2.3', 170, 171, null],
                                ['Publicidad', 1, '5.1.2.4', 172, 173, null],
                            ]
                        ],
                        ['GASTOS DE ADMINISTRACION', 0, '5.1.3', 175, 196, [
                                ['Haberes administrativos', 1, '5.1.3.1', 176, 177, null],
                                ['Cargas sociales administrativas', 1, '5.1.3.2', 178, 179, null],
                                ['Honorarios', 1, '5.1.3.3', 180, 181, null],
                                ['Alquileres', 1, '5.1.3.4', 182, 183, null],
                                ['Servicios de luz, agua, etc', 1, '5.1.3.5', 184, 185, null],
                                ['Mantenimiento y limpieza', 1, '5.1.3.6', 186, 187, null],
                                ['Artículos librería', 1, '5.1.3.7', 188, 189, null],
                                ['Cadetería y correo', 1, '5.1.3.8', 190, 191, null],
                                ['Seguros', 1, '5.1.3.9', 192, 193, null],
                                ['Diferencias por Redondeo', 1, '5.1.3.10', 194, 195, null],
                            ]
                        ],
                        ['GASTOS FISCALES', 0, '5.1.4', 197, 204, [
                                ['Impuestos ingresos brutos', 1, '5.1.4.1', 198, 199, null],
                                ['Impuestos a las ganancias', 1, '5.1.4.2', 200, 201, null],
                                ['Impuestos transacciones bancarias', 1, '5.1.4.3', 202, 203, null],
                            ]
                        ],
                        ['OTROS GASTOS ', 0, '5.1.5', 205, 208, [
                                ['Intereses y recargos', 1, '5.1.5.1', 206, 207, null],
                            ]
                        ],
                    ],
                ],
                ['GASTOS NO OPERATIVOS', 0, '5.2', 210, 221, [
                        ['AMORTIZACIONES', 0, '5.2.1', 211, 220, [
                                ['Amortizaciones instalaciones', 1, '5.2.1.1', 212, 213, null],
                                ['Amortizaciones equipos de computación', 1, '5.2.1.2', 214, 215, null],
                                ['Amortizacion software', 1, '5.2.1.3', 216, 217, null],
                                ['Amortizacion muebles y útiles', 1, '5.2.1.4', 218, 219, null],
                                ['Cuenta Corriente', 1, '1.1.1.6', 14, 15, null]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    private function insertAccount($values, $parentId=null)
    {
        $this->insert("account", [
            'name' => $values[0],
            'is_usable' => $values[1],
            'code' => $values[2],
            'lft' => $values[3],
            'rgt' => $values[4],
            'parent_account_id' => $parentId] );
        $id = Yii::$app->db->getLastInsertID();

        if(is_array($values[5])) {
            foreach($values[5] as $value) {
                $this->insertAccount($value, $id);
            }
        }
    }

    public function down()
    {
        echo "m150804_184839_accounts cannot be reverted.\n";

        return false;
    }
}
