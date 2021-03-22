<?php

use yii\db\Schema;
use yii\db\Migration;

class m151009_200611_westnet_accounts extends Migration
{
    public function up()
    {
        $this->delete('account');

        $this->insertAccount(
            ['ACTIVO',0,'',0,0, [
                ['ACTIVO CORRIENTE',0,'',0,0,[
                    ['CAJA Y BANCOS',0,'',0,0,[
                        ['CAJA CHICA',1,'',0,0,null],
                        ['CAJA ECOPAGOS',1,'',0,0,null],
                        ['CAJA GODOY CRUZ',1,'',0,0,null],
                        ['BANCO 108',1,'',0,0,null],
                        ['BANCO 104',1,'',0,0,null],
                        ['BANCO CREDICOOP GABRIEL C/C',1,'',0,0,null],
                        ['BANCO CREDICOOP MAURICIO C/C',1,'',0,0,null],
                        ['BANCO MACRO C/C',1,'',0,0,null],
                        ['BANCO FRANCES C/C CHEQUES',1,'',0,0,null],
                        ['BANCO FRANCES C/C EFECTIVO',1,'',0,0,null],
                        ['BANCO SUPERVIELLE C/C',1,'',0,0,null],
                        ['BANCO STANDARD BANK C/C',1,'',0,0,null]]
                    ],
                    ['ECOPAGOS',0,'',0,0,[
                        ['LAVALLE',1,'',0,0,null],
                        ['COSTA',1,'',0,0,null],
                        ['PLANET EXPRESS',1,'',0,0,null],
                        ['PASEO LA BODEGA',1,'',0,0,null],
                        ['RAKU',1,'',0,0,null],
                        ['CORRALITOS- ONCE',1,'',0,0,null],
                        ['CORRALITOS – RUIZ',1,'',0,0,null],
                        ['RUTTINI',1,'',0,0,null],
                        ['COMODO',1,'',0,0,null],
                        ['PANQUEHUA',1,'',0,0,null],
                        ['WALMART',1,'',0,0,null],
                        ['PEDRIAL',1,'',0,0,null],
                        ['COLONIA SEGOVIA- DELEGACION',1,'',0,0,null],
                        ['COLONIA SEOVIA- VARO',1,'',0,0,null]]
                    ],
                    ['CUENTAS POR COBRAR',0,'',0,0,[
                        ['DEUDORES POR VENTAS',1,'',0,0,null],
                        ['DEUDORES VARIOS',1,'',0,0,null],
                        ['DEUDORES MOROSOS',1,'',0,0,null]],
                    ],
                    ['DOCUMENTOS A COBRAR',1,'',0,0,null],
                    ['PRESTAMOS OTORGADOS',1,'',0,0,null],
                    ['PREVISION PARA DEUDORES INCOBRABLES',1,'',0,0,null],
                    ['ANTICIPO IMP A LAS GANANCIAS',1,'',0,0,null],
                    ['ANTICIPO A PROVEEDORES',1,'',0,0,null],
                    ['IVA SALDO A FAVOR',1,'',0,0,null],
                    ['IVA CREDITO FISCAL',1,'',0,0,null],
                    ['CUENTA PARTICULAR GABRIEL LAS HERAS',1,'',0,0,null],
                    ['CUENTA PARTICULAR MAURICIO PUERTA',1,'',0,0,null],
                    ['RETENCIONES',0,'',0,0, [
                        ['RETENCION IVA',1,'',0,0,null],
                        ['RETENCION GANANCIAS',1,'',0,0,null],
                        ['RETENCION IIBB',1,'',0,0,null],
                        ['RETENCION SUSSS',1,'',0,0,null]]
                    ],
                ]],
                ['ACTIVO NO CORRIENTE',0,'',0,0,[
                    ['BIENES DE USO',0,'',0,0,[
                        ['EQUIPOS DE INTERNET',1,'',0,0,null],
                        ['INMUEBLES',1,'',0,0,null],
                        ['AMORTIZACION ACUMULADA INMUEBLES',1,'',0,0,null],
                        ['RODADOS',1,'',0,0,null],
                        ['AMORTIZACION ACUMULADA RODADOS',1,'',0,0,null],
                        ['MUEBLES Y UTILES',1,'',0,0,null],
                        ['AMORTIZACION ACUMULADA MUEBLES Y UTILES',1,'',0,0,null],
                        ['EQUIPOS DE COMPUTACION',1,'',0,0,null],
                        ['INSTALACIONES',1,'',0,0,null]]
                    ]
                ]]]]);

        $this->insertAccount(
            ['PASIVO',0,'',0,0, [
                ['PROVEEDORES',0,'',0,0,[
                    ['SILICA NETWORKS',1,'',0,0,null],
                    ['TELEFONICA DE ARGENTINA',1,'',0,0,null],
                    ['INFOANDINA',1,'',0,0,null],
                    ['SOLUTION BOX',1,'',0,0,null],
                    ['LAUFQUEN',1,'',0,0,null],
                    ['MICROCOM',1,'',0,0,null],
                    ['ELECTRONICA MENDOZA',1,'',0,0,null],
                    ['FERGUSON JOSE CARLOS',1,'',0,0,null]]
                ],
                ['DOC A PAGAR',1,'',0,0,null],
                ['ACREEDORES VARIOS',1,'',0,0,null],
                ['ANTICIPOS DE CLIENTES',1,'',0,0,null],
                ['IVA DEBITO FISCAL',1,'',0,0,null],
                ['IVA A PAGAR',1,'',0,0,null],
                ['SUSS A PAGAR',1,'',0,0,null],
                ['IIBB A PAGAR',1,'',0,0,null],
                ['HONORARIOS A PAGAR',1,'',0,0,null]
            ]]);

        $this->insertAccount(
            ['PATRIMONIO NETO',0,'',0,0,[
                ['CAPITAL',1,'',0,0,null],
                ['RESERVA LEGAL',1,'',0,0,null],
                ['RESULTADO DEL EJERCICIO',1,'',0,0,null],
                ['RESULTADOS NO ASIGNADOS',1,'',0,0,null]]
            ]);

        $this->insertAccount(
            ['INGRESOS',0,'',0,0,[
                ['VENTAS',0,'',0,0,[
                    ['ALQUILER MENSUAL EQUIPOS',1,'',0,0,null],
                    ['INSTALACION EQUIPOS',1,'',0,0,null],
                    ['PROVISION HADWARE',1,'',0,0,null],
                    ['PLAN DE PAGOS',1,'',0,0,null]
                ]],
                ['ALQUILERES GANADOS',1,'',0,0,null],
                ['INTERESES GANADOS',1,'',0,0,null],
                ['PRESTACION EXTERNA INTERNET',1,'',0,0,null]
            ]
            ]);

        $this->insertAccount(
            ['EGRESOS',0,'',0,0,[
                ['COSTO LABORAL',0,'',0,0,[
                    ['SUELDOS Y JORNALES',1,'',0,0,null],
                    ['CONTRIBUCION OBRA SOCIAL',1,'',0,0,null],
                    ['CEC',1,'',0,0,null],
                    ['931',1,'',0,0,null],
                    ['FAECYS',1,'',0,0,null]]],
                ['ALQUILERES',0,'',0,0,[
                    ['OFICINA 108',1,'',0,0,null],
                    ['OFICINA 104',1,'',0,0,null],
                    ['GODOY CRUZ',1,'',0,0,null],
                    ['PATRICIAS',1,'',0,0,null],
                    ['RODADOS',1,'',0,0,null],
                    ['HERRAMIENTAS',1,'',0,0,null],
                    ['NODOS',1,'',0,0,null]]],
                ['MANTENIMIENTO Y REPARACIONES',0,'',0,0,[
                    ['INMUEBLES',1,'',0,0,null],
                    ['RODADOS',1,'',0,0,null],
                    ['EQUIPOS DE COMPUTACION',1,'',0,0,null],
                    ['NANOS',1,'',0,0,null],
                    ['INSTALACIONES',1,'',0,0,null],
                    ['MUEBLES Y UTILES',1,'',0,0,null]]],
                ['COMUNICACIONES',0,'',0,0,[
                    ['FIJAS',1,'',0,0,null],
                    ['MOVILES',1,'',0,0,null],
                    ['INTERNET',1,'',0,0,null]]],
                ['COMBUSTIBLES Y LUBRICANTES',0,'',0,0,[
                    ['GNC',1,'',0,0,null],
                    ['NAFTA',1,'',0,0,null],
                    ['GASOIL',1,'',0,0,null],
                    ['IMPUESTOS INTERNOS',1,'',0,0,null]]],
                ['NO GRAVADOS',1,'',0,0,null],
                ['REFRIGERIOS',1,'',0,0,null],
                ['ENERGIA ELECTRICA',0,'',0,0,[
                    ['OFICINA 108',1,'',0,0,null],
                    ['OFICINA 104',1,'',0,0,null],
                    ['GODOY CRUZ',1,'',0,0,null],
                    ['PATRICIAS',1,'',0,0,null]]],
                ['GAS',1,'',0,0,null],
                ['AGUAS MENDOCINAS',0,'',0,0,[
                    ['OFICINA 108',1,'',0,0,null],
                    ['OFICINA 104',1,'',0,0,null],
                    ['GODOY CRUZ',1,'',0,0,null],
                    ['PATRICIAS',1,'',0,0,null]]],
                ['MUNICIPALIDAD',0,'',0,0,[
                    ['OFICINA 108',1,'',0,0,null],
                    ['OFICINA 104',1,'',0,0,null],
                    ['GODOY CRUZ',1,'',0,0,null],
                    ['PATRICIAS',1,'',0,0,null]]],
                ['PROMOCION Y PUBLICIDAD',1,'',0,0,null],
                ['LIMPIEZA',1,'',0,0,null],
                ['HONORARIOS PROFESIONALES',1,'',0,0,null],
                ['ESTACIONAMIENTO',1,'',0,0,null],
                ['ROPA DE TRABAJO',1,'',0,0,null],
                ['LICENCIAS INFORMATICAS',1,'',0,0,null],
                ['IMPUESTO DE SELLOS',1,'',0,0,null],
                ['FLETES Y ENCOMIENDAS',1,'',0,0,null],
                ['SEGUROS Y CAUCIONES',1,'',0,0,null],
                ['EXENTOS',1,'',0,0,null],
                ['VIATICOS, PASAJES Y REPRESENTACIONES',1,'',0,0,null],
                ['LIBRERÍA Y PAPELERIA',1,'',0,0,null],
                ['FOTOCOPIAS',1,'',0,0,null],
                ['INSUMOS DE COMPUTACION',1,'',0,0,null],
                ['SEGUROS',0,'',0,0,[
                    ['VIDA',1,'',0,0,null],
                    ['INMUEBLES',1,'',0,0,null],
                    ['RODADOS',1,'',0,0,null]]],
                ['GASTOS BANCARIOS',1,'',0,0,null],
                ['IMPUESTOS',0,'',0,0,[
                    ['IIBB',1,'',0,0,null],
                    ['AUTOMOTOR',1,'',0,0,null],
                    ['INMOBILIARIO',1,'',0,0,null]]]
            ]]);
    }

    public function down()
    {
        echo "m151009_200611_westnet_accounts cannot be reverted.\n";

        return false;
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
}
