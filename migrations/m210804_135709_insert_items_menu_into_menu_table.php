<?php

use yii\db\Migration;

/**
 * Class m210804_135709_insert_items_menu_into_menu_table
 */
class m210804_135709_insert_items_menu_into_menu_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('menu', [
            'menu_id' => 1,
            'description' => 'Home',
            'icon' => 'glyphicon glyphicon-home',
            'route' => '/site/index',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******Start Facturación items*******
        $this->insert('menu', [
            'menu_id' => 2,
            'description' => 'Facturación',
            'icon' => 'glyphicon glyphicon-home',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 3,
            'description' => 'Cupón de Pago',
            'icon' => '',
            'route' => '/sale/bill/create?type=6',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 4,
            'description' => 'Factura A',
            'icon' => '',
            'route' => '/sale/bill/create?type=1',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 5,
            'description' => 'Factura B',
            'icon' => '',
            'route' => '/sale/bill/create?type=2',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 6,
            'description' => 'Factura M',
            'icon' => '',
            'route' => '/sale/bill/create?type=14',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 7,
            'description' => 'Descuento',
            'icon' => '',
            'route' => '/sale/bill/create?type=8',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 8,
            'description' => 'Nota Crédito A',
            'icon' => '',
            'route' => '/sale/bill/create?type=4',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 9,
            'description' => 'Nota Crédito B',
            'icon' => '',
            'route' => '/sale/bill/create?type=11',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 10,
            'description' => 'Nota Crédito M',
            'icon' => '',
            'route' => '/sale/bill/create?type=17',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 11,
            'description' => 'Adicional',
            'icon' => '',
            'route' => '/sale/bill/create?type=9',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 12,
            'description' => 'Nota Débito A',
            'icon' => '',
            'route' => '/sale/bill/create?type=5',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 13,
            'description' => 'Nota Débito B',
            'icon' => '',
            'route' => '/sale/bill/create?type=10',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 14,
            'description' => 'Nota Débito M',
            'icon' => '',
            'route' => '/sale/bill/create?type=16',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 15,
            'description' => 'Facturación por Lotes',
            'icon' => '',
            'route' => '/sale/batch-invoice/index',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 16,
            'description' => 'Facturación por lotes con filtros',
            'icon' => '',
            'route' => '/sale/batch-invoice/index-with-filters',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 17,
            'description' => 'Cerrar Facturas de lote pendientes',
            'icon' => '',
            'route' => '/sale/batch-invoice/close-invoices-index',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 18,
            'description' => 'Facturación por Cliente',
            'icon' => '',
            'route' => '/sale/bill/invoice-customer',
            'menu_idmenu' => 2,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Facturación items*******

        //*******Start Vendedores items*******

        $this->insert('menu', [
            'menu_id' => 19,
            'description' => 'Vendedores',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 20,
            'description' => 'Alta Cliente',
            'icon' => '',
            'route' => '/sale/customer/sell',
            'menu_idmenu' => 19,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Vendedores items*******

        //*******Start Comprobantes items*******

        $this->insert('menu', [
            'menu_id' => 21,
            'description' => 'Comprobantes',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 22,
            'description' => 'Configuración',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 21,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 23,
            'description' => 'Tipos de comprobante',
            'icon' => '',
            'route' => '/sale/bill-type/index',
            'menu_idmenu' => 22,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 24,
            'description' => 'Unidades',
            'icon' => '',
            'route' => '/sale/unit/index',
            'menu_idmenu' => 22,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 25,
            'description' => 'Monedas',
            'icon' => '',
            'route' => '/sale/currency/index',
            'menu_idmenu' => 22,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 26,
            'description' => 'Impuestos',
            'icon' => '',
            'route' => '/sale/tax/index',
            'menu_idmenu' => 22,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 27,
            'description' => 'Tasas impositivas',
            'icon' => '',
            'route' => '/sale/tax-rate/index',
            'menu_idmenu' => 22,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 28,
            'description' => 'Condiciones frente al IVA',
            'icon' => '',
            'route' => '/sale/tax-condition/index',
            'menu_idmenu' => 22,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 29,
            'description' => 'Clases Facturación E.',
            'icon' => '',
            'route' => '/sale/invoice-class/index',
            'menu_idmenu' => 22,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 30,
            'description' => 'Resumen de comprobantes',
            'icon' => '',
            'route' => '/sale/bill/history',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 31,
            'description' => 'Mis Comprobantes',
            'icon' => '',
            'route' => '/sale/bill?user_id=${user_id}',
            'menu_idmenu' => 21,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 32,
            'description' => 'Todos los comprobantes',
            'icon' => '',
            'route' => '/sale/bill',
            'menu_idmenu' => 21,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 33,
            'description' => 'Todos las facturas',
            'icon' => '',
            'route' => '/sale/bill?class=Bill',
            'menu_idmenu' => 21,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 34,
            'description' => 'IVA Ventas',
            'icon' => '',
            'route' => '/afip/taxes-book/sale',
            'menu_idmenu' => 21,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 35,
            'description' => 'IVA Compras',
            'icon' => '',
            'route' => '/afip/taxes-book/buy',
            'menu_idmenu' => 21,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 36,
            'description' => 'Productos para IIBB',
            'icon' => '',
            'route' => '/afip/taxes-book/iibb-products',
            'menu_idmenu' => 21,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Comprobantes items*******

        //*******Start Clientes items*******

        $this->insert('menu', [
            'menu_id' => 37,
            'description' => 'Clientes',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 38,
            'description' => 'Clientes',
            'icon' => '',
            'route' => '/sale/customer/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 39,
            'description' => 'Panel de cobranza',
            'icon' => '',
            'route' => '/sale/customer/cashing-panel',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 40,
            'description' => 'Clientes con saldo a favor',
            'icon' => '',
            'route' => '/sale/customer/positive-balance-customers',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 41,
            'description' => 'Instalaciones Pendientes',
            'icon' => '',
            'route' => '/sale/customer/pending-installations',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 42,
            'description' => 'Instalaciones Realizadas',
            'icon' => '',
            'route' => '/sale/customer/installations',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 43,
            'description' => 'Pagos',
            'icon' => '',
            'route' => '/checkout/payment/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 44,
            'description' => 'Informes de pago',
            'icon' => '',
            'route' => '/westnet/notify-payment',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 45,
            'description' => 'Cambios de velocidad programados',
            'icon' => '',
            'route' => '/sale/contract/programmed-plan-change/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 46,
            'description' => 'Perfiles adicionales',
            'icon' => '',
            'route' => '/sale/profile-class/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 47,
            'description' => 'Tipos de Doc.',
            'icon' => '',
            'route' => '/sale/document-type/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 48,
            'description' => 'Condiciones frente a IVA',
            'icon' => '',
            'route' => '/sale/tax-condition/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);


        $this->insert('menu', [
            'menu_id' => 49,
            'description' => 'Horarios del Cliente',
            'icon' => '',
            'route' => '/sale/hour-range/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 50,
            'description' => 'Categorías de Cliente',
            'icon' => '',
            'route' => '/sale/customer-class/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 51,
            'description' => 'Rubros de Cliente',
            'icon' => '',
            'route' => '/sale/customer-category/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 52,
            'description' => 'Zonas',
            'icon' => '',
            'route' => '/zone/zone/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 53,
            'description' => 'Descuentos',
            'icon' => '',
            'route' => '/sale/discount/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 54,
            'description' => 'Canales de publicidad',
            'icon' => '',
            'route' => '/sale/publicity-shape/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 55,
            'description' => 'Medios de pago',
            'icon' => '',
            'route' => '/checkout/payment-method/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 56,
            'description' => 'Facturado y Cobrado',
            'icon' => '',
            'route' => '/sale/customer/billed-and-cashed',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 57,
            'description' => 'Enviar comprobantes por email masivamente',
            'icon' => '',
            'route' => '/sale/bill/get-last-bills',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 58,
            'description' => 'Mensaje SMS Predefinidos al Cliente',
            'icon' => '',
            'route' => '/sale/customer-message/index',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 59,
            'description' => 'Verificar Emails',
            'icon' => '',
            'route' => '/sale/customer/verify-emails',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 60,
            'description' => 'Deudores',
            'icon' => '',
            'route' => '/sale/customer/debtors',
            'menu_idmenu' => 37,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Clientes items*******

        //*******Start Productos items*******
        $this->insert('menu', [
            'menu_id' => 61,
            'description' => 'Productos',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 62,
            'description' => 'Productos',
            'icon' => '',
            'route' => '/sale/product/index',
            'menu_idmenu' => 61,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 63,
            'description' => 'Categorías',
            'icon' => '',
            'route' => '/sale/category/index',
            'menu_idmenu' => 61,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 64,
            'description' => 'Planes',
            'icon' => '',
            'route' => '/sale/contract/plan/index',
            'menu_idmenu' => 61,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 65,
            'description' => 'Características de Plan',
            'icon' => '',
            'route' => '/sale/contract/plan-feature/index',
            'menu_idmenu' => 61,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 66,
            'description' => 'Importación de Productos',
            'icon' => '',
            'route' => '/import/importer/import',
            'menu_idmenu' => 61,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 67,
            'description' => 'Movimientos de Stock',
            'icon' => '',
            'route' => '/sale/stock-movement/index',
            'menu_idmenu' => 61,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Productos items*******

        //*******Start Pagos items*******

        $this->insert('menu', [
            'menu_id' => 68,
            'description' => 'Pagos',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 69,
            'description' => 'Medios de pago',
            'icon' => '',
            'route' => '/checkout/payment-method/index',
            'menu_idmenu' => 68,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 70,
            'description' => 'Planes de pago',
            'icon' => '',
            'route' => '/checkout/payment-plan/list',
            'menu_idmenu' => 68,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 71,
            'description' => 'Archivos de Pago Fácil',
            'icon' => '',
            'route' => '/checkout/payment/pagofacil-payments-index',
            'menu_idmenu' => 68,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 72,
            'description' => 'Exportar para Pagomiscuentas',
            'icon' => '',
            'route' => '/pagomiscuentas/export/index',
            'menu_idmenu' => 68,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 73,
            'description' => 'Importar para Pagomiscuentas',
            'icon' => '',
            'route' => '/pagomiscuentas/import/index',
            'menu_idmenu' => 68,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 74,
            'description' => 'Bancos para Débito Directo',
            'icon' => '',
            'route' => '/automaticdebit/bank/index',
            'menu_idmenu' => 68,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 75,
            'description' => 'Débito Directo',
            'icon' => '',
            'route' => '/automaticdebit/automatic-debit/index',
            'menu_idmenu' => 68,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 76,
            'description' => 'Débito Automático Firstdata',
            'icon' => '',
            'route' => '/firstdata/firstdata-automatic-debit/index',
            'menu_idmenu' => 68,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 77,
            'description' => 'Configuraciones Empresas Firstdata',
            'icon' => '',
            'route' => '/firstdata/firstdata-company-config/index',
            'menu_idmenu' => 68,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 78,
            'description' => 'Exportaciones Firstdata',
            'icon' => '',
            'route' => '/firstdata/firstdata-export/index',
            'menu_idmenu' => 68,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 79,
            'description' => 'Importaciones Firstdata',
            'icon' => '',
            'route' => '/firstdata/firstdata-import/index',
            'menu_idmenu' => 68,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Pagos items*******

        //*******End Pagos items*******

        $this->insert('menu', [
            'menu_id' => 80,
            'description' => 'Reportes',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 81,
            'description' => 'Reportes por empresa',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 82,
            'description' => 'Clientes Activos por Mes',
            'icon' => '',
            'route' => '/reports/reports-company/customers-per-month',
            'menu_idmenu' => 81,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 83,
            'description' => 'Variación de Clientes por Mes',
            'icon' => '',
            'route' => '/reports/reports-company/custumer-variation-per-month',
            'menu_idmenu' => 81,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 84,
            'description' => 'Facturas Adeudadas',
            'icon' => '',
            'route' => '/reports/reports-company/debt-bills',
            'menu_idmenu' => 81,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 85,
            'description' => 'Bajas por Mes',
            'icon' => '',
            'route' => '/reports/reports-company/low-by-month',
            'menu_idmenu' => 81,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 86,
            'description' => 'Rentabilidad',
            'icon' => '',
            'route' => '/reports/reports-company/cost-effectiveness',
            'menu_idmenu' => 81,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 87,
            'description' => 'Variacion Total de Clientes',
            'icon' => '',
            'route' => '/reports/reports-company/up-down-variation',
            'menu_idmenu' => 81,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 88,
            'description' => 'Ingresos y Egresos',
            'icon' => '',
            'route' => '/reports/reports-company/in-out',
            'menu_idmenu' => 81,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 89,
            'description' => 'Historial de cambios de empresa',
            'icon' => '',
            'route' => '/reports/customer/change-company-history',
            'menu_idmenu' => 81,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 90,
            'description' => 'Alta de Clientes',
            'icon' => '',
            'route' => '/reports/reports/customer-registrations',
            'menu_idmenu' => 81,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 91,
            'description' => 'Intenciones de Pago',
            'icon' => '',
            'route' => '/reports/reports-company/payment-intention',
            'menu_idmenu' => 81,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 92,
            'description' => 'Clientes Activos por Mes',
            'icon' => '',
            'route' => '/reports/reports/customers-per-month',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 93,
            'description' => 'Variación de Clientes por Mes',
            'icon' => '',
            'route' => '/reports/reports/costumer-variation-per-month',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 94,
            'description' => 'Porcentaje de Pasivo',
            'icon' => '',
            'route' => '/reports/reports/company-passive',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 95,
            'description' => 'Facturas Adeudadas',
            'icon' => '',
            'route' => '/reports/reports/debt-bills',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 96,
            'description' => 'Bajas por Mes',
            'icon' => '',
            'route' => '/reports/reports-company/low-by-month',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 97,
            'description' => 'Bajas por Razón',
            'icon' => '',
            'route' => '/reports/reports/low-by-reason',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 98,
            'description' => 'Rentabilidad',
            'icon' => '',
            'route' => '/reports/reports/cost-effectiveness',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 99,
            'description' => 'Variación Total de Clientes',
            'icon' => '',
            'route' => '/reports/reports/up-down-variation',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 100,
            'description' => 'Ingresos y Egresos',
            'icon' => '',
            'route' => '/reports/reports/in-out',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 101,
            'description' => 'Medios de pago',
            'icon' => '',
            'route' => '/reports/reports/payment-methods',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 102,
            'description' => 'Clientes por Nodo',
            'icon' => '',
            'route' => '/reports/reports/customers-by-node',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 103,
            'description' => 'Clientes por Velocidad',
            'icon' => '',
            'route' => '/reports/reports/customers-by-speed',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 104,
            'description' => 'Clientes por canales de publicidad',
            'icon' => '',
            'route' => '/reports/reports/customer-by-publicity-shape',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 105,
            'description' => 'Reporte de tickets',
            'icon' => '',
            'route' => '/ticket/ticket/report',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 106,
            'description' => 'Historial de extensiones de pago',
            'icon' => '',
            'route' => '/westnet/payment-extension-history/index',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 107,
            'description' => 'Gráficos de informes de pago',
            'icon' => '',
            'route' => '/reports/reports/notify-payments-graphics',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 108,
            'description' => 'Gráficos de extensiones de pago',
            'icon' => '',
            'route' => '/reports/reports/payment-extension-graphics',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 109,
            'description' => 'Reporte de Clientes Actualizados',
            'icon' => '',
            'route' => '/reports/customer/customers-updated',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 110,
            'description' => 'Clientes Actualizados por Usuario',
            'icon' => '',
            'route' => '/reports/customer/customers-updated-by-user',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 111,
            'description' => 'Reporte Firstdata débito automatico',
            'icon' => '',
            'route' => '/reports/reports/firstdata-debit-report',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 112,
            'description' => 'Reporte de aplicación móvil',
            'icon' => '',
            'route' => '/reports/reports/mobile-app',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 113,
            'description' => 'Reporte de notificaciones push',
            'icon' => '',
            'route' => '/reports/reports/push-notifications-report',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 114,
            'description' => 'Descuentos',
            'icon' => '',
            'route' => '/reports/reports/discount',
            'menu_idmenu' => 80,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Pagos items*******

        //*******Start Proveedores items*******

        $this->insert('menu', [
            'menu_id' => 115,
            'description' => 'Proveedores',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 116,
            'description' => 'Proveedores',
            'icon' => '',
            'route' => '/provider/provider/index',
            'menu_idmenu' => 115,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);


        $this->insert('menu', [
            'menu_id' => 117,
            'description' => 'Deuda a proveedores',
            'icon' => '',
            'route' => '/provider/provider/debts',
            'menu_idmenu' => 115,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 118,
            'description' => 'Comprobantes de proveedor',
            'icon' => '',
            'route' => '/provider/provider-bill/index',
            'menu_idmenu' => 115,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 119,
            'description' => 'Pagos a proveedor',
            'icon' => '',
            'route' => '/provider/provider-payment/index',
            'menu_idmenu' => 115,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 120,
            'description' => 'Facturas y Pagos de Proveedores',
            'icon' => '',
            'route' => '/provider/provider/bills-and-payments',
            'menu_idmenu' => 115,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Proveedores items*******

        //*******End Empleados items*******

        $this->insert('menu', [
            'menu_id' => 121,
            'description' => 'Empleados',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 122,
            'description' => 'Empleados',
            'icon' => '',
            'route' => '/employee/employee/index',
            'menu_idmenu' => 121,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 123,
            'description' => 'Deuda a empleados',
            'icon' => '',
            'route' => '/employee/employee/debts',
            'menu_idmenu' => 121,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 124,
            'description' => 'Comprobantes de empleados',
            'icon' => '',
            'route' => '/employee/employee-bill/index',
            'menu_idmenu' => 121,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 125,
            'description' => 'Pagos de empleados',
            'icon' => '',
            'route' => '/employee/employee-payment/index',
            'menu_idmenu' => 121,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 126,
            'description' => 'Comprobantes y pagos de empleados',
            'icon' => '',
            'route' => '/employee/employee/bills-and-payments',
            'menu_idmenu' => 121,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 127,
            'description' => 'Categorías de Empleado',
            'icon' => '',
            'route' => '/employee/employee-category/index',
            'menu_idmenu' => 121,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Empleados items*******

        //*******End Contabilidad items*******

        $this->insert('menu', [
            'menu_id' => 128,
            'description' => 'Contabilidad',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 129,
            'description' => 'Entidades Monetarias',
            'icon' => '',
            'route' => '/accounting/money-box/index',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 130,
            'description' => 'Cuentas Monetarias',
            'icon' => '',
            'route' => '/accounting/money-box-account/index',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 131,
            'description' => 'Tipos de Entidad Monetaria',
            'icon' => '',
            'route' => '/accounting/money-box-type/index',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 132,
            'description' => 'Resúmenes bancarios',
            'icon' => '',
            'route' => '/accounting/resume/index',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 133,
            'description' => 'Conciliaciones',
            'icon' => '',
            'route' => '/accounting/conciliation/index',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 134,
            'description' => 'Tipos de Operaciones',
            'icon' => '',
            'route' => '/accounting/operation-type/index',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 135,
            'description' => 'Asiento Manual',
            'icon' => '',
            'route' => '/accounting/account-movement/create',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 136,
            'description' => 'Libro Diario',
            'icon' => '',
            'route' => '/accounting/account-movement/index',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 137,
            'description' => 'Libro Maestro',
            'icon' => '',
            'route' => '/accounting/account-movement/resume',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 138,
            'description' => 'Periodos Contables',
            'icon' => '',
            'route' => '/accounting/accounting-period/index',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 139,
            'description' => 'PLan de Cuentas',
            'icon' => '',
            'route' => '/accounting/account/index',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 140,
            'description' => 'Configuraciones de Cuentas',
            'icon' => '',
            'route' => '/accounting/account-config/index',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 141,
            'description' => 'Cheques',
            'icon' => '',
            'route' => '/paycheck/paycheck/index',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 142,
            'description' => 'Chequeras',
            'icon' => '',
            'route' => '/paycheck/checkbook/index',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 143,
            'description' => 'CAJA CHICA',
            'icon' => '',
            'route' => '/paycheck/checkbook/index',
            'menu_idmenu' => 128,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Contabilidad items*******

        //*******Start Aplicación items*******

        $this->insert('menu', [
            'menu_id' => 144,
            'description' => 'Aplicación',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 145,
            'description' => 'Logs',
            'icon' => '',
            'route' => '/log/log/index',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 146,
            'description' => 'Copias de seguridad',
            'icon' => '',
            'route' => '/backup/backup/index',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 147,
            'description' => 'Empresas',
            'icon' => '',
            'route' => '/sale/company',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 148,
            'description' => 'Puntos de Venta',
            'icon' => '',
            'route' => '/sale/point-of-sale',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 149,
            'description' => 'Configuración de Facturación',
            'icon' => '',
            'route' => '/sale/company-has-billing',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 150,
            'description' => 'Configuración de Emails',
            'icon' => '',
            'route' => '/mailing/email-transport/index',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 151,
            'description' => 'Contabilidad',
            'icon' => '',
            'route' => '/config/config?category=1',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 152,
            'description' => 'Comprobantes',
            'icon' => '',
            'route' => '/config/config?category=2',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 153,
            'description' => 'Gestión de Stock',
            'icon' => '',
            'route' => '/config/config?category=3',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 154,
            'description' => 'Agenda',
            'icon' => '',
            'route' => '/config/config?category=4',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 155,
            'description' => 'Media',
            'icon' => '',
            'route' => '/config/config?category=5',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 156,
            'description' => 'Productos',
            'icon' => '',
            'route' => '/config/config?category=6',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 157,
            'description' => 'Customer',
            'icon' => '',
            'route' => '/config/config?category=7',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 158,
            'description' => 'Sequre',
            'icon' => '',
            'route' => '/config/config?category=8',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 159,
            'description' => 'Westnet',
            'icon' => '',
            'route' => '/config/config?category=9',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 160,
            'description' => 'Ecopago',
            'icon' => '',
            'route' => '/config/config?category=10',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 161,
            'description' => 'General',
            'icon' => '',
            'route' => '/config/config?category=11',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 162,
            'description' => 'Socios',
            'icon' => '',
            'route' => '/config/config?category=12',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 163,
            'description' => 'Ticket',
            'icon' => '',
            'route' => '/config/config?category=13',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 164,
            'description' => 'Notificaciones por Correo',
            'icon' => '',
            'route' => '/config/config?category=16',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 165,
            'description' => 'Vendedores',
            'icon' => '',
            'route' => '/config/config?category=17',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 166,
            'description' => 'Pagomiscuentas',
            'icon' => '',
            'route' => '/config/config?category=18',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 167,
            'description' => 'Mobile App',
            'icon' => '',
            'route' => '/config/config?category=19',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 168,
            'description' => 'ADS',
            'icon' => '',
            'route' => '/config/config?category=20',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 169,
            'description' => 'Notificaciones',
            'icon' => '',
            'route' => '/config/config?category=21',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 170,
            'description' => 'Infobip',
            'icon' => '',
            'route' => '/config/config?category=22',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 171,
            'description' => 'Backups',
            'icon' => '',
            'route' => '/config/config?category=23',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 172,
            'description' => 'Firstdata',
            'icon' => '',
            'route' => '/config/config?category=24',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 173,
            'description' => 'Categorías de configuración',
            'icon' => '',
            'route' => '/config/category',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 174,
            'description' => 'Ítems de configuración',
            'icon' => '',
            'route' => '/config/item',
            'menu_idmenu' => 144,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Aplicación items*******

        //*******End Westnet items*******

        $this->insert('menu', [
            'menu_id' => 175,
            'description' => 'Westnet',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 176,
            'description' => 'Socio',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 177,
            'description' => 'Socio',
            'icon' => '',
            'route' => '/partner/partner',
            'menu_idmenu' => 176,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 178,
            'description' => 'Modelos de Distribución Societaría',
            'icon' => '',
            'route' => '/partner/partner-distribution-model',
            'menu_idmenu' => 176,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 179,
            'description' => 'Liquidación',
            'icon' => '',
            'route' => '/partner/liquidation',
            'menu_idmenu' => 176,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 180,
            'description' => 'Liquidaciones',
            'icon' => '',
            'route' => '/partner/liquidation/list-liquidation',
            'menu_idmenu' => 176,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 181,
            'description' => 'Servidores',
            'icon' => '',
            'route' => '/westnet/server',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 182,
            'description' => 'Nodos',
            'icon' => '',
            'route' => '/westnet/node',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 183,
            'description' => 'Vendedores',
            'icon' => '',
            'route' => '/westnet/vendor',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 184,
            'description' => 'IPs asignadas',
            'icon' => '',
            'route' => '/westnet/node/assigned-ip',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 185,
            'description' => 'Networks',
            'icon' => '',
            'route' => '/westnet/ip-range/index',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 186,
            'description' => 'Access Point',
            'icon' => '',
            'route' => '/westnet/access-point/index',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 187,
            'description' => 'Ads vacíos no usados',
            'icon' => '',
            'route' => '/westnet/empty-ads/index',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 188,
            'description' => 'Crear ADS vacíos',
            'icon' => '',
            'route' => '/westnet/ads/print-empty-ads',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 189,
            'description' => 'Ecopagos',
            'icon' => '',
            'route' => '/westnet/ecopagos/ecopago',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 190,
            'description' => 'Cobradores',
            'icon' => '',
            'route' => '/westnet/ecopagos/cashier',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 191,
            'description' => 'Recaudadores',
            'icon' => '',
            'route' => '/westnet/ecopagos/collector',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 192,
            'description' => 'Pagos en Ecopagos',
            'icon' => '',
            'route' => '/westnet/ecopagos/payout',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 193,
            'description' => 'Cierres de lote',
            'icon' => '',
            'route' => '/westnet/ecopagos/batch-closure',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 194,
            'description' => 'Cierres diarios',
            'icon' => '',
            'route' => '/westnet/ecopagos/daily-closure',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 195,
            'description' => 'Registros fallidos App Mobile',
            'icon' => '',
            'route' => '/mobileapp/v1/app-failed-register/index',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 196,
            'description' => 'Notificaciones',
            'icon' => '',
            'route' => '/westnet/notifications/notification',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 197,
            'description' => 'Transportes',
            'icon' => '',
            'route' => '/westnet/notifications/transport',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 198,
            'description' => 'Filtros de SMS recibidos',
            'icon' => '',
            'route' => '/westnet/notifications/integratech-sms-filter',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 199,
            'description' => 'Mensajes recibidos de integratech',
            'icon' => '',
            'route' => '/westnet/notifications/integratech-received-sms',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 200,
            'description' => 'SMS enviados por Infobip',
            'icon' => '',
            'route' => '/westnet/notifications/infobip/default/sended-messages',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 201,
            'description' => 'Mensajes recibidos de Infobip',
            'icon' => '',
            'route' => '/westnet/notifications/infobip/default/index',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 202,
            'description' => 'Asignación de Descuento a Clientes',
            'icon' => '',
            'route' => '/westnet/batch/discount-to-customer',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 203,
            'description' => 'Asignación de Plan a Clientes',
            'icon' => '',
            'route' => '/westnet/batch/plans-to-customer',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 204,
            'description' => 'Asignación de Sucursal a Clientes',
            'icon' => '',
            'route' => '/westnet/batch/company-to-customer',
            'menu_idmenu' => 175,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Westnet items*******

        //*******End Ayuda items*******

        $this->insert('menu', [
            'menu_id' => 205,
            'description' => 'Ayuda',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 206,
            'description' => 'Instructivo',
            'icon' => '',
            'route' => '/instructive/instructive/index',
            'menu_idmenu' => 205,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 207,
            'description' => 'Categoria de Instructivo',
            'icon' => '',
            'route' => '/instructive/instructive-category/index',
            'menu_idmenu' => 205,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Ayuda items*******


        //*******End Tickets items*******

        $this->insert('menu', [
            'menu_id' => 208,
            'description' => 'Tickets',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 209,
            'description' => 'Tickets',
            'icon' => '',
            'route' => '/ticket/ticket/open-tickets',
            'menu_idmenu' => 208,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 210,
            'description' => 'Tickets de gestión de cobranza',
            'icon' => '',
            'route' => '/ticket/ticket/collection-tickets',
            'menu_idmenu' => 208,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 211,
            'description' => 'Tickets de gestión de instalaciones',
            'icon' => '',
            'route' => '/ticket/ticket/installations-tickets',
            'menu_idmenu' => 208,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 212,
            'description' => 'Tickets de edición de datos desde aplicación',
            'icon' => '',
            'route' => '/ticket/ticket/contact-edition-tickets',
            'menu_idmenu' => 208,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 213,
            'description' => 'Alta Ticket',
            'icon' => '',
            'route' => '/ticket/ticket/create',
            'menu_idmenu' => 208,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 214,
            'description' => 'Clientes con tickets abiertos',
            'icon' => '',
            'route' => '/ticket/ticket/list',
            'menu_idmenu' => 208,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 215,
            'description' => 'Acciones generadas',
            'icon' => '',
            'route' => '/ticket/action',
            'menu_idmenu' => 208,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 216,
            'description' => 'Estados de ticket',
            'icon' => '',
            'route' => '/ticket/status',
            'menu_idmenu' => 208,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 217,
            'description' => 'Colores de ticket',
            'icon' => '',
            'route' => '/ticket/color',
            'menu_idmenu' => 208,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 218,
            'description' => 'Esquema de estados',
            'icon' => '',
            'route' => '/ticket/schema',
            'menu_idmenu' => 208,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 219,
            'description' => 'Categorías de ticket',
            'icon' => '',
            'route' => '/ticket/category',
            'menu_idmenu' => 208,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Tickets items*******


        //*******End Agenda items*******

        $this->insert('menu', [
            'menu_id' => 220,
            'description' => 'Agenda',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 221,
            'description' => 'Mi agenda',
            'icon' => '',
            'route' => '/agenda',
            'menu_idmenu' => 220,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 222,
            'description' => 'Tareas',
            'icon' => '',
            'route' => '/agenda/task',
            'menu_idmenu' => 220,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 223,
            'description' => 'Crear Tarea',
            'icon' => '',
            'route' => 'http://localhost/agenda',
            'menu_idmenu' => 220,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 224,
            'description' => 'Categorías de Tarea',
            'icon' => '',
            'route' => '/agenda/category',
            'menu_idmenu' => 220,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 225,
            'description' => 'Tipos de Tarea',
            'icon' => '',
            'route' => '/agenda/task-type',
            'menu_idmenu' => 220,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 226,
            'description' => 'Estados de Tarea',
            'icon' => '',
            'route' => '/agenda/status',
            'menu_idmenu' => 220,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 227,
            'description' => 'Tipos de Evento',
            'icon' => '',
            'route' => '/agenda/task-type',
            'menu_idmenu' => 220,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Agenda items*******


        //*******End Usuarios items*******

        $this->insert('menu', [
            'menu_id' => 228,
            'description' => 'Usuarios',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 229,
            'description' => 'Usuarios',
            'icon' => '',
            'route' => '/user-management/user/index',
            'menu_idmenu' => 228,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 230,
            'description' => 'Roles',
            'icon' => '',
            'route' => '/user-management/role/index',
            'menu_idmenu' => 228,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 231,
            'description' => 'Permisos',
            'icon' => '',
            'route' => '/user-management/permission/index',
            'menu_idmenu' => 228,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 232,
            'description' => 'Grupos',
            'icon' => '',
            'route' => '/user-management/auth-item-group/index',
            'menu_idmenu' => 228,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 233,
            'description' => 'Log de acceso',
            'icon' => '',
            'route' => '/user-management/user-visit-log/index',
            'menu_idmenu' => 228,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End Usuarios items*******


        //*******End User items*******

        $this->insert('menu', [
            'menu_id' => 234,
            'description' => '${user}',
            'icon' => '',
            'route' => '#',
            'menu_idmenu' => 0,
            'status' => 1,
            'is_submenu' => 0,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 235,
            'description' => 'Logout',
            'icon' => '',
            'route' => '/user-management/auth/logout',
            'menu_idmenu' => 234,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 236,
            'description' => 'Cambiar clave',
            'icon' => '',
            'route' => '/user-management/auth/change-own-password',
            'menu_idmenu' => 234,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 237,
            'description' => 'Recuperar clave',
            'icon' => '',
            'route' => '/user-management/auth/password-recovery',
            'menu_idmenu' => 234,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        $this->insert('menu', [
            'menu_id' => 238,
            'description' => 'Confirmar email',
            'icon' => '',
            'route' => '/user-management/auth/confirm-email',
            'menu_idmenu' => 234,
            'status' => 1,
            'is_submenu' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        //*******End User items*******


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
          
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210804_135709_insert_items_menu_into_menu_table cannot be reverted.\n";

        return false;
    }
    */
}
