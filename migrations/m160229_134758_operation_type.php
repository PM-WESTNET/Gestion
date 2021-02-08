<?php

use app\modules\accounting\models\Account;
use yii\db\Schema;
use yii\db\Migration;

class m160229_134758_operation_type extends Migration
{
    public function up()
    {
        $this->insert('operation_type', [
            'name'=> 'AFIP',
            'code' => 'AFIP',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'AFIP PLAN DE PAGO',
            'code' => 'AFIP PLAN DE PAGO',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'CANJE INTERNO',
            'code' => 'CANJE INTERNO',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'COMISION CAJA DE SEGURIDAD',
            'code' => 'COMISION CAJA DE SEGURIDAD',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'COMISION GESTION DE VALORES',
            'code' => 'COMISION GESTION DE VALORES',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'COMISION TRANSFERENCIA',
            'code' => 'COMISION TRANSFERENCIA',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'COMISIONES PAGO FACIL',
            'code' => 'COMISIONES PAGO FACIL',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'COMP ELECT CABAL',
            'code' => 'COMP ELECT CABAL',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'COMPENSACION VALORES',
            'code' => 'COMPENSACION VALORES',
            'is_debit' => 1]);

        $this->insert('operation_type', [
            'name'=> 'DEBITO PRESTAMO (ESCUDERO )',
            'code' => 'DEBITO PRESTAMO (ESCUDERO )',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'DEP BANCO FONDO REP',
            'code' => 'DEP BANCO FONDO REP',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'DEP MONEDA EXTRANJERA',
            'code' => 'DEP MONEDA EXTRANJERA',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'DEPOSITO CHEQUE DE UN SOCIO (PROPIO',
            'code' => 'DEPOSITO CHEQUE DE UN SOCIO (PROPIO',
            'is_debit' => 1]);

        $this->insert('operation_type', [
            'name'=> 'DEPOSITO DE CHEQUES (NO USAR)',
            'code' => 'DEPOSITO DE CHEQUES (NO USAR)',
            'is_debit' => 1]);

        $this->insert('operation_type', [
            'name'=> 'DEPOSITO DE EFECTIVO DE UN SOCIO',
            'code' => 'DEPOSITO DE EFECTIVO DE UN SOCIO',
            'is_debit' => 1]);

        $this->insert('operation_type', [
            'name'=> 'DEPOSITO EFECTIVO DE CAJA CHICA',
            'code' => 'DEPOSITO EFECTIVO DE CAJA CHICA',
            'is_debit' => 1]);

        $this->insert('operation_type', [
            'name'=> 'DEVOLUCION DE IVA',
            'code' => 'DEVOLUCION DE IVA',
            'is_debit' => 1]);

        $this->insert('operation_type', [
            'name'=> 'EMISION DE CHEQUES PROPIO (NO USAR)',
            'code' => 'EMISION DE CHEQUES PROPIO (NO USAR)',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'EXTRACCION DE EFECTIVO A CAJA CHICA',
            'code' => 'EXTRACCION DE EFECTIVO A CAJA CHICA',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'EXTRACCION EFECTIVO DE SOCIOS',
            'code' => 'EXTRACCION EFECTIVO DE SOCIOS',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'IMPUESTO DEBITO-CREDITO',
            'code' => 'IMPUESTO DEBITO-CREDITO',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'IMPUTACION POR RECHAZOS',
            'code' => 'IMPUTACION POR RECHAZOS',
            'is_debit' => 1]);

        $this->insert('operation_type', [
            'name'=> 'IVA DEBITO FISCAL',
            'code' => 'IVA DEBITO FISCAL',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'IVA TASA GENERAL',
            'code' => 'IVA TASA GENERAL',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'LEY 25413',
            'code' => 'LEY 25413',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'MANTENIMIENTO DE CUENTA',
            'code' => 'MANTENIMIENTO DE CUENTA',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'MOVIMIENTO ENTRE CUENTAS',
            'code' => 'MOVIMIENTO ENTRE CUENTAS',
            'is_debit' => 1]);

        $this->insert('operation_type', [
            'name'=> 'PERCEPCION IVA',
            'code' => 'PERCEPCION IVA',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'PROVINCIA SEGURO',
            'code' => 'PROVINCIA SEGURO',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'REC IIBB S/CTOS BRIOS',
            'code' => 'REC IIBB S/CTOS BRIOS',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'REC ING BRUTOS SAN LUIS',
            'code' => 'REC ING BRUTOS SAN LUIS',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'RECHAZO CHEQUES CLIENTES',
            'code' => 'RECHAZO CHEQUES CLIENTES',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'RECHAZO DE CHEQUE PROPIO',
            'code' => 'RECHAZO DE CHEQUE PROPIO',
            'is_debit' => 1]);

        $this->insert('operation_type', [
            'name'=> 'REGIMEN DE RETENCIONES',
            'code' => 'REGIMEN DE RETENCIONES',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'REINTEGRO MULTA CHEQUE',
            'code' => 'REINTEGRO MULTA CHEQUE',
            'is_debit' => 1]);

        $this->insert('operation_type', [
            'name'=> 'RESUMEN Y GASTOS',
            'code' => 'RESUMEN Y GASTOS',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'RETIRO EFECTIVO CAJA AUT',
            'code' => 'RETIRO EFECTIVO CAJA AUT',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'SEGURO DE VIDA',
            'code' => 'SEGURO DE VIDA',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'SEGURO ROBO CAJERO',
            'code' => 'SEGURO ROBO CAJERO',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'SUSCRIPCION PERIOD',
            'code' => 'SUSCRIPCION PERIOD',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'TARJETA DEBITO DE UN SOCIO',
            'code' => 'TARJETA DEBITO DE UN SOCIO',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'TRANS RECIB DE CAJA PARA PASE CTA',
            'code' => 'TRANS RECIB DE CAJA PARA PASE CTA',
            'is_debit' => 1]);

        $this->insert('operation_type', [
            'name'=> 'TRANSF EMITIDA A CAJA PARA PASE CTA',
            'code' => 'TRANSF EMITIDA A CAJA PARA PASE CTA',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'TRANSFERENCA A SOCIOS .',
            'code' => 'TRANSFERENCA A SOCIOS .',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'TRANSFERENCIA DEBITO',
            'code' => 'TRANSFERENCIA DEBITO',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'TRANSFERENCIAS ENTRE BANCOS',
            'code' => 'TRANSFERENCIAS ENTRE BANCOS',
            'is_debit' => 0]);

        $this->insert('operation_type', [
            'name'=> 'VARIOS D',
            'code' => 'VARIOS D',
            'is_debit' => 1]);

        $this->insert('operation_type', [
            'name'=> 'VARIOS H',
            'code' => 'VARIOS H',
            'is_debit' => 0]);

        echo 'Recuerde modificar los valores de la cuenta de los tipos de operacion.'."\n";
    }


    public function down()
    {
        $this->delete('operation_type', ['code' => 'AFIP' ]);
        $this->delete('operation_type', ['code' => 'AFIP PLAN DE PAGO' ]);
        $this->delete('operation_type', ['code' => 'CANJE INTERNO' ]);
        $this->delete('operation_type', ['code' => 'COMISION CAJA DE SEGURIDAD' ]);
        $this->delete('operation_type', ['code' => 'COMISION GESTION DE VALORES' ]);
        $this->delete('operation_type', ['code' => 'COMISION TRANSFERENCIA' ]);
        $this->delete('operation_type', ['code' => 'COMISIONES PAGO FACIL' ]);
        $this->delete('operation_type', ['code' => 'COMP ELECT CABAL' ]);
        $this->delete('operation_type', ['code' => 'COMPENSACION VALORES' ]);
        $this->delete('operation_type', ['code' => 'DEBITO PRESTAMO (ESCUDERO )' ]);
        $this->delete('operation_type', ['code' => 'DEP BANCO FONDO REP' ]);
        $this->delete('operation_type', ['code' => 'DEP MONEDA EXTRANJERA' ]);
        $this->delete('operation_type', ['code' => 'DEPOSITO CHEQUE DE UN SOCIO (PROPIO' ]);
        $this->delete('operation_type', ['code' => 'DEPOSITO DE CHEQUES (NO USAR)' ]);
        $this->delete('operation_type', ['code' => 'DEPOSITO DE EFECTIVO DE UN SOCIO' ]);
        $this->delete('operation_type', ['code' => 'DEPOSITO EFECTIVO DE CAJA CHICA' ]);
        $this->delete('operation_type', ['code' => 'DEVOLUCION DE IVA' ]);
        $this->delete('operation_type', ['code' => 'EMISION DE CHEQUES PROPIO (NO USAR)' ]);
        $this->delete('operation_type', ['code' => 'EXTRACCION DE EFECTIVO A CAJA CHICA' ]);
        $this->delete('operation_type', ['code' => 'EXTRACCION EFECTIVO DE SOCIOS' ]);
        $this->delete('operation_type', ['code' => 'IMPUESTO DEBITO-CREDITO' ]);
        $this->delete('operation_type', ['code' => 'IMPUTACION POR RECHAZOS' ]);
        $this->delete('operation_type', ['code' => 'IVA DEBITO FISCAL' ]);
        $this->delete('operation_type', ['code' => 'IVA TASA GENERAL' ]);
        $this->delete('operation_type', ['code' => 'LEY 25413' ]);
        $this->delete('operation_type', ['code' => 'MANTENIMIENTO DE CUENTA' ]);
        $this->delete('operation_type', ['code' => 'MOVIMIENTO ENTRE CUENTAS' ]);
        $this->delete('operation_type', ['code' => 'PERCEPCION IVA' ]);
        $this->delete('operation_type', ['code' => 'PROVINCIA SEGURO' ]);
        $this->delete('operation_type', ['code' => 'REC IIBB S/CTOS BRIOS' ]);
        $this->delete('operation_type', ['code' => 'REC ING BRUTOS SAN LUIS' ]);
        $this->delete('operation_type', ['code' => 'RECHAZO CHEQUES CLIENTES' ]);
        $this->delete('operation_type', ['code' => 'RECHAZO DE CHEQUE PROPIO' ]);
        $this->delete('operation_type', ['code' => 'REGIMEN DE RETENCIONES' ]);
        $this->delete('operation_type', ['code' => 'REINTEGRO MULTA CHEQUE' ]);
        $this->delete('operation_type', ['code' => 'RESUMEN Y GASTOS' ]);
        $this->delete('operation_type', ['code' => 'RETIRO EFECTIVO CAJA AUT' ]);
        $this->delete('operation_type', ['code' => 'SEGURO DE VIDA' ]);
        $this->delete('operation_type', ['code' => 'SEGURO ROBO CAJERO' ]);
        $this->delete('operation_type', ['code' => 'SUSCRIPCION PERIOD' ]);
        $this->delete('operation_type', ['code' => 'TARJETA DEBITO DE UN SOCIO' ]);
        $this->delete('operation_type', ['code' => 'TRANS RECIB DE CAJA PARA PASE CTA' ]);
        $this->delete('operation_type', ['code' => 'TRANSF EMITIDA A CAJA PARA PASE CTA' ]);
        $this->delete('operation_type', ['code' => 'TRANSFERENCA A SOCIOS .' ]);
        $this->delete('operation_type', ['code' => 'TRANSFERENCIA DEBITO' ]);
        $this->delete('operation_type', ['code' => 'TRANSFERENCIAS ENTRE BANCOS' ]);
        $this->delete('operation_type', ['code' => 'VARIOS D' ]);
        $this->delete('operation_type', ['code' => 'VARIOS H' ]);

        return true;
    }
}