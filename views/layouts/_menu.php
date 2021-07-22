<?php

use app\components\widgets\agenda\notification\Notification;
use app\components\widgets\agenda\task\Task;
use yii\helpers\Html;
use yii\bootstrap\NavBar;
use \webvimark\modules\UserManagement\components\GhostNav;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;
use app\components\widgets\Nav;
use app\modules\sale\components\BillExpert;
use app\modules\westnet\reports\ReportsModule;
use webvimark\modules\UserManagement\models\User;
use app\modules\westnet\notifications\NotificationsModule;
use app\modules\westnet\models\Vendor;
use app\modules\sale\models\BillType;

//Fix ancho de submenu NavX > DropdownX
$this->registerCss('.dropdown-submenu .dropdown-menu { right: auto; }');

$items = [];
$alwaysVisibleItems = [];

//Home
$alwaysVisibleItems[] = ['label' => Yii::t('app', 'Home'), 'url' => ['/site/index']];

if (UserHelper::isCashier()) {
    $alwaysVisibleItems[] = [
                'label' => Yii::t('westnet', 'Ecopago'),
                'url' => ['/westnet/ecopagos/frontend/site/index'], 'visible' => !Yii::$app->user->isGuest,
                'linkOptions' => ['class' => 'btn btn-info navbar-btn', 'target' => '_blank'],
    ];
}

//Vendedores
$vendorItems = [
    ['label' => Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('app', 'Customer')]), 'url' => ['/sale/customer/sell']],
    ['label' => Yii::t('app', 'My Sales'), 'url' => ['/sale/contract/contract/vendor-list'], 'visible' => Vendor::vendorExists(Yii::$app->user->id)]
];
$items[] = ['label' => Yii::t('app', 'Sellers'), 'items' => $vendorItems];



//Vender
$billTypes2Create = BillType::find()->orderBy(['class' => SORT_ASC, 'name' => SORT_ASC])->where(['startable' => 1])->all();
$billItems = [];
$lastClass = null;
foreach ($billTypes2Create as $item) {
    if ($lastClass != null && $item->class != $lastClass) {
        $billItems[] = '<li class="divider"></li>';
    }
    $lastClass = $item->class;
    $billItems[] = ['label' => $item->name, 'url' => ['/sale/bill/create', 'type' => $item->bill_type_id]];
}
// Agrego Facturacion masiva
$billItems[] = '<li class="divider"></li>';
$billItems[] = ['label' => Yii::t('app', 'Batch Invoice'), 'url' => ['/sale/batch-invoice/index']];
$billItems[] = ['label' => Yii::t('app', 'Batch Invoice with filters'), 'url' => ['/sale/batch-invoice/index-with-filters']];
$billItems[] = ['label' => Yii::t('app','Close Pending Batch Invoices'), 'url' => ['/sale/batch-invoice/close-invoices-index']];
$billItems[] = '<li class="divider"></li>';
$billItems[] = ['label' => Yii::t('app', 'Customer Invoice'), 'url' => ['/sale/bill/invoice-customer']];

if (User::hasRole('batch-invoice-rol')) {
    $alwaysVisibleItems[] = ['label' => Yii::t('app', 'Billing'), 'items'=>$billItems];
}

//Bills (indexes y otros modelos relacionados):
$billIndexItems = [
    ['label' => Yii::t('app', 'Configuration'), 'items' => [
            ['label' => Yii::t('app', 'Bill Types'), 'url' => ['/sale/bill-type/index'], 'visible' => Yii::$app->user->isSuperadmin],
            ['label' => Yii::t('app', 'Units'), 'url' => ['/sale/unit/index'], 'visible' => Yii::$app->user->isSuperadmin],
            ['label' => Yii::t('app', 'Currencies'), 'url' => ['/sale/currency/index'], 'visible' => Yii::$app->user->isSuperadmin],
            ['label' => '<li class="divider"></li>', 'visible' => Yii::$app->user->isSuperadmin],
            ['label' => Yii::t('app', 'Taxes'), 'url' => ['/sale/tax/index'], 'visible' => Yii::$app->user->isSuperadmin],
            ['label' => Yii::t('app', 'Tax Rates'), 'url' => ['/sale/tax-rate/index'], 'visible' => Yii::$app->user->isSuperadmin],
            ['label' => Yii::t('app', 'Tax Conditions'), 'url' => ['/sale/tax-condition/index'], 'visible' => Yii::$app->user->isSuperadmin],
            //'<li class="divider"></li>',

            ['label' => '<li class="divider"></li>', 'visible' => Yii::$app->user->isSuperadmin],
            ['label' => Yii::t('app', 'Invoice Classes'), 'url' => ['/sale/invoice-class/index'], 'visible' => Yii::$app->user->isSuperadmin]
    ], 'visible' => Yii::$app->user->isSuperadmin],

];
if (User::hasRole('batch-invoice-rol')) {
    $billIndexItems = array_merge($billIndexItems, [
        ['label' => Yii::t('app', 'Bills summary'), 'url' => ['/sale/bill/history']],
        ['label' => Yii::t('app', 'My Bills'), 'url' => ['/sale/bill', 'BillSearch[user_id]' => Yii::$app->user->id]],
        '<li class="divider"></li>',
        ['label' => Yii::t('app', 'All Bills'), 'url' => ['/sale/bill', 'BillSearch[class]' => '']],
    ]);
}

//En este menu debemos mostrar un index por cada tipo de comprobante
$billTypes2Index = BillType::find()->orderBy(['class' => SORT_ASC, 'name' => SORT_ASC])->all();
if($billTypes2Index){
    $billIndexItems[] ='<li class="divider"></li>';
}

if (!User::hasRole('seller')) {

    foreach ($billTypes2Index as $item) {

        $billIndexItems[] = ['label' => Yii::t('app', 'List: {name}', [
                'name' => \app\components\helpers\Inflector::getInflector()->pluralize($item->name)
            ]), 'url' => ['/sale/bill/index', 'BillSearch[bill_types][]' => "$item->bill_type_id"]
        ];
    }
}

if (User::hasRole('batch-invoice-rol')) {
    $billIndexItems = array_merge($billIndexItems, [
        ['label' => Yii::t('app', 'All Invoices'), 'url' => ['/sale/bill', 'BillSearch[class]' => 'Bill']],
    ]);
}

$items[] = ['label' => Yii::t('app', 'Bills'), 'items' => $billIndexItems];

//Menu de afip
if (Yii::$app->getModule('afip')) {
    $items[count($items) - 1]['items'][] = '<li class="divider"></li>';
    $items[count($items) - 1]['items'][] = ['label' => Yii::t('afip', 'IVA Sale'), 'url' => ['/afip/taxes-book/sale']];
    $items[count($items) - 1]['items'][] = ['label' => Yii::t('afip', 'IVA Buy'), 'url' => ['/afip/taxes-book/buy']];
    $items[count($items) - 1]['items'][] = '<li class="divider"></li>';
    $items[count($items) - 1]['items'][] = ['label' => Yii::t('afip', 'Products for IIBB'), 'url' => ['/afip/taxes-book/iibb-products']];
}

//if (!User::hasRole('seller')){
    //Clientes
    $items[] = ['label' => Yii::t('app','Customers'), 'items'=>[
        ['label'=>Yii::t('app','Customers'), 'url'=>['/sale/customer/index']],
        ['label'=>Yii::t('app','Cashing panel'), 'url'=>['/sale/customer/cashing-panel']],
    ['label'=>Yii::t('app', 'Positive Balance Customers'), 'url'=>['/sale/customer/positive-balance-customers']],
        ['label'=> Yii::t('app', 'Pending Installations'), 'url' =>['/sale/customer/pending-installations']],
        ['label'=> Yii::t('app', 'Installations'), 'url' =>['/sale/customer/installations']],
        //'<li class="divider"></li>',
        ['label'=>Yii::t('app','Payments'), 'url'=>['/checkout/payment/index']],
        ['label'=>Yii::t('app','Notify payments'), 'url'=>['/westnet/notify-payment']],
        ['label'=>Yii::t('app','Programmed plan changes'), 'url'=>['/sale/contract/programmed-plan-change/index']],
        //'<li class="divider"></li>',
        ['label'=>Yii::t('app','Profile Classes'), 'url'=>['/sale/profile-class/index']],
        ['label'=>'<li class="divider"></li>', 'encode'=>false],
        ['label'=>Yii::t('app','Document Types'), 'url'=>['/sale/document-type/index']],
        ['label'=>Yii::t('app','Tax Conditions'), 'url'=>['/sale/tax-condition/index']],
        ['label'=>Yii::t('app','Customer Hour range'), 'url'=>['/sale/hour-range/index']],
        //'<li class="divider"></li>',
        ['label'=>Yii::t('app','Customer Classes'), 'url'=>['/sale/customer-class/index']],
        ['label'=>Yii::t('app','Customer Categories'), 'url'=>['/sale/customer-category/index']],
        ['label' => Yii::t('app','Zones'), 'url' => ['/zone/zone/index'], 'visible' => User::canRoute(['/zone/zone/index'])],
        //'<li class="divider"></li>',
        ['label'=>Yii::t('app','Discounts'), 'url'=>['/sale/discount/index']],
        ['label'=>Yii::t('app','Publicity Shapes'), 'url'=>['/sale/publicity-shape/index']],
        ['label' => Yii::t('app', 'Payment Methods'), 'url' => ['/checkout/payment-method/index'], 'visible' => User::canRoute('/checkout/payment-method/index')],
        '<li class="divider"></li>',
        ['label'=>Yii::t('app','Billed and Cashed'), 'url'=>['/sale/customer/billed-and-cashed']],
        '<li class="divider"></li>',
        ['label'=>Yii::t('app','Enviar comprobantes por email masivamente'), 'url'=>['/sale/bill/get-last-bills'], 'visible' => User::canRoute('/sale/bill/get-last-bills')],
        ['label'=>Yii::t('app','Predefined Customer SMS Messages'), 'url'=>['/sale/customer-message/index']],
        ['label'=>Yii::t('app','Verify Emails'), 'url'=>['/sale/customer/verify-emails']],
        ['label'=>Yii::t('app','Customer Debts'), 'url'=>['/sale/customer/debtors']],
    ]];
//}
//Productos
$productItems = [];
$productItems[] = ['label' => Yii::t('app', 'Products'), 'url' => ['/sale/product/index']];
$productItems[] = ['label' => Yii::t('app', 'Categories'), 'url' => ['/sale/category/index']];

//Esta activado el manejo de planes?


if (Yii::$app->params['plan_product']) {
    //$productItems[] = '<li class="divider"></li>';
    $productItems[] = ['label' => Yii::t('app', 'Plans'), 'url' => ['/sale/contract/plan/index']];
    $productItems[] = ['label' => Yii::t('app', 'Plan Features'), 'url' => ['/sale/contract/plan-feature/index']];
}

//Agregamos el menu si el modulo es cargado
if (Yii::$app->getModule('import')) {
    //$productItems[] = '<li class="divider"></li>';
    $productItems[] = ['label' => Yii::t('import', 'Product importer'), 'url' => ['/import/importer/import']];
}

//$productItems[] = '<li class="divider"></li>';
$productItems[] = ['label' => Yii::t('app', 'Stock movements'), 'url' => ['/sale/stock-movement/index']];

$items[] = ['label' => Yii::t('app', 'Products'), 'items' => $productItems];

//Pagos
if (Yii::$app->getModule('checkout')) {
    $items[] = ['label' => Yii::t('app', 'Payments'), 'items' => [
            ['label' => Yii::t('app', 'Payment Methods'), 'url' => ['/checkout/payment-method/index']],
            ['label' => Yii::t('app', 'Payment Plans'), 'url' => ['/checkout/payment-plan/list']],
            ['label' => Yii::t('app', 'Pago Fácil Files'), 'url' => ['/checkout/payment/pagofacil-payments-index']],
            '<li class="divider"></li>',
            ['label'=>Yii::t('pagomiscuentas','Export Pagomiscuentas'), 'url'=>['/pagomiscuentas/export/index']],
            ['label'=>Yii::t('pagomiscuentas','Import Pagomiscuentas'), 'url'=>['/pagomiscuentas/import/index']],
            '<li class="divider"></li>',
            ['label'=>Yii::t('app','Banks for Automatic Debit'), 'url'=>['/automaticdebit/bank/index']],
            ['label'=>Yii::t('app','Automatic Debit'), 'url'=>['/automaticdebit/automatic-debit/index']],
            '<li class="divider"></li>',
            ['label'=>Yii::t('app','Firstdata Automatic Debit'), 'url'=>['/firstdata/firstdata-automatic-debit/index']],
            ['label'=>Yii::t('app','Firstdata Company Configs'), 'url'=>['/firstdata/firstdata-company-config/index']],
            ['label'=>Yii::t('app','Firstdata Exports'), 'url'=>['/firstdata/firstdata-export/index']],
            ['label'=>Yii::t('app','Firstdata Imports'), 'url'=>['/firstdata/firstdata-import/index']],

    ]];
}

//Reportes
if (Yii::$app->getModule('reports')) {
    $items[] = ['label' => ReportsModule::t('app', 'Reports'), 'items' => [
        ['label' => ReportsModule::t('app', 'Company Reports'), 'items' => [
            ['label' => ReportsModule::t('app', 'Active Customers per month'), 'url' => ['/reports/reports-company/customers-per-month']],
            ['label' => ReportsModule::t('app', 'Customers Variation per month'), 'url' => ['/reports/reports-company/custumer-variation-per-month']],
            ['label' => ReportsModule::t('app', 'Debt Bills'), 'url' => ['/reports/reports-company/debt-bills']],
            ['label' => ReportsModule::t('app', 'Low By Month'), 'url' => ['/reports/reports-company/low-by-month']],
            ['label' => ReportsModule::t('app', 'Cost effectiveness'), 'url' => ['/reports/reports-company/cost-effectiveness']],
            ['label' => ReportsModule::t('app', 'Total Customer Variation'), 'url' => ['/reports/reports-company/up-down-variation']],
            ['label' => ReportsModule::t('app', 'Ingresos y Egresos'), 'url' => ['/reports/reports-company/in-out']],

            ['label' => ReportsModule::t('app', 'Change History company'), 'url' => ['/reports/customer/change-company-history']],

            ['label' => ReportsModule::t('app', 'Customer Registrations'), 'url' => ['/reports/reports/customer-registrations']],
        ]],
        ['label' => ReportsModule::t('app', 'Active Customers per month'), 'url' => ['/reports/reports/customers-per-month']],
        ['label' => ReportsModule::t('app', 'Customers Variation per month'), 'url' => ['/reports/reports/costumer-variation-per-month']],
        ['label' => ReportsModule::t('app', 'Company Passive'), 'url' => ['/reports/reports/company-passive']],
        ['label' => ReportsModule::t('app', 'Debt Bills'), 'url' => ['/reports/reports/debt-bills']],
        ['label' => ReportsModule::t('app', 'Low By Month'), 'url' => ['/reports/reports/low-by-month']],
        ['label' => ReportsModule::t('app', 'Low By Reason'), 'url' => ['/reports/reports/low-by-reason']],
        ['label' => ReportsModule::t('app', 'Cost effectiveness'), 'url' => ['/reports/reports/cost-effectiveness']],
        ['label' => ReportsModule::t('app', 'Total Customer Variation'), 'url' => ['/reports/reports/up-down-variation']],
        ['label' => ReportsModule::t('app', 'Ingresos y Egresos'), 'url' => ['/reports/reports/in-out']],
        ['label' => ReportsModule::t('app', 'Payment Methods'), 'url' => ['/reports/reports/payment-methods']],
        ['label' => Yii::t('app', 'Customers By Node'), 'url' => ['/reports/reports/customers-by-node']],
        ['label' => Yii::t('app', 'Customers By Speed'), 'url' => ['/reports/reports/customers-by-speed']],
        ['label' => Yii::t('app', 'Customers by publicity shape'), 'url' => ['/reports/reports/customer-by-publicity-shape']],
        ['label' => Yii::t('app', 'Tickets report'), 'url' => ['/ticket/ticket/report']],
        ['label' => Yii::t('app', 'Payment extension history'), 'url' => ['/westnet/payment-extension-history/index']],
        ['label' => Yii::t('app', 'Notify payments graphics'), 'url' => ['/reports/reports/notify-payments-graphics']],
        ['label' => Yii::t('app', 'Payment extension graphic'), 'url' => ['/reports/reports/payment-extension-graphics']],
        ['label' => Yii::t('app', 'Updated Customers Report'), 'url' => ['/reports/customer/customers-updated']],
        ['label' => Yii::t('app', 'Updated Customers Report By User'), 'url' => ['/reports/customer/customers-updated-by-user']],
        ['label' => Yii::t('app', 'Firstdata Automatic Debit Report'), 'url' => ['/reports/reports/firstdata-debit-report']],
        '<li class="divider"></li>',
        ['label' => ReportsModule::t('app', 'Mobile app report'), 'url' => ['/reports/reports/mobile-app']],
        ['label' => ReportsModule::t('app', 'Push notifications report'), 'url' => ['/reports/reports/push-notifications-report']],
        '<li class="divider"></li>',
        ['label' => "Descuentos", 'url' => ['/reports/reports/discount']],
    ]];
}

//Proveedores
if (Yii::$app->getModule('provider')) {
    $items[] = ['label' => Yii::t('app', 'Providers'), 'items' => [
            ['label' => Yii::t('app', 'Providers'), 'url' => ['/provider/provider/index']],
            ['label' => Yii::t('app', 'Provider Debts'), 'url' => ['/provider/provider/debts']],
            '<li class="divider"></li>',
            ['label' => Yii::t('app', 'Provider Bills'), 'url' => ['/provider/provider-bill/index']],
            ['label' => Yii::t('app', 'Provider Payments'), 'url' => ['/provider/provider-payment/index']],
            '<li class="divider"></li>',
            ['label' => Yii::t('app', 'Provider Bills And Payments'), 'url' => ['/provider/provider/bills-and-payments']],
    ]];
}

//Empleados
if (Yii::$app->getModule('employee')) {
    $items[] = ['label' => Yii::t('app', 'Employees'), 'items' => [
        ['label' => Yii::t('app', 'Employees'), 'url' => ['/employee/employee/index']],
        ['label' => Yii::t('app', 'Employee Debts'), 'url' => ['/employee/employee/debts']],
        '<li class="divider"></li>',
        ['label' => Yii::t('app', 'Employee Bills'), 'url' => ['/employee/employee-bill/index']],
        ['label' => Yii::t('app', 'Employee Payments'), 'url' => ['/employee/employee-payment/index']],
        '<li class="divider"></li>',
        ['label' => Yii::t('app', 'Employee Bills And Payments'), 'url' => ['/employee/employee/bills-and-payments']],
        '<li class="divider"></li>',
        ['label' => Yii::t('app', 'Employee Categories'), 'url' => ['/employee/employee-category/index']],
    ]];
}

//Accounting
if (Yii::$app->getModule('accounting')) {
    $items[] = ['label' => Yii::t('accounting', 'accounting'), 'items' => [
        ['label' => Yii::t('accounting', 'Money Boxes'), 'url' => ['/accounting/money-box/index']],
        ['label' => Yii::t('accounting', 'Money Box Accounts'), 'url' => ['/accounting/money-box-account/index']],
        ['label' => Yii::t('accounting', 'Money Box Types'), 'url' => ['/accounting/money-box-type/index']],
        //'<li class="divider"></li>',
        ['label' => Yii::t('accounting', 'Resumes'), 'url' => ['/accounting/resume/index']],
        ['label' => Yii::t('accounting', 'Conciliations'), 'url' => ['/accounting/conciliation/index']],
        ['label' => Yii::t('accounting', 'Operation Types'), 'url' => ['/accounting/operation-type/index']],
        //'<li class="divider"></li>',
        ['label' => Yii::t('accounting', 'Manual Entry'), 'url' => ['/accounting/account-movement/create']],
        ['label' => Yii::t('accounting', 'Diary Book'), 'url' => ['/accounting/account-movement/index']],
        ['label' => Yii::t('accounting', 'Master Book'), 'url' => ['/accounting/account-movement/resume']],
        ['label' => Yii::t('accounting', 'Accounting Periods'), 'url' => ['/accounting/accounting-period/index']],
        ['label' => Yii::t('accounting', 'Account Plan'), 'url' => ['/accounting/account/index']],
        ['label' => Yii::t('accounting', 'Account Configs'), 'url' => ['/accounting/account-config/index'], 'visible' => Yii::$app->user->isSuperadmin],
    ]];

    if (Yii::$app->getModule('paycheck')) {
        //$items[count($items) - 1]['items'][] = '<li class="divider"></li>';
        $items[count($items) - 1]['items'][] = ['label' => Yii::t('paycheck', 'Paychecks'), 'url' => ['/paycheck/paycheck/index']];
        $items[count($items) - 1]['items'][] = ['label' => Yii::t('paycheck', 'Checkbooks'), 'url' => ['/paycheck/checkbook/index']];
    }

    $smallBoxes = app\modules\accounting\models\MoneyBoxAccount::findDailyBoxes();
    if($smallBoxes){
        $items[count($items) - 1]['items'][] = '<li class="divider"></li>';
    }
    foreach($smallBoxes as $small){
        $items[count($items) - 1]['items'][] = ['label' => Yii::t('accounting', $small->account->name), 'url' => ['/accounting/money-box-account/daily-box-movements', 'id' => $small->money_box_account_id]];
    }
}
$appMenu = [];
if (User::canRoute('/log/index')) {
    $appMenu = [
        ['label' => Yii::t('app', 'Logs'), 'url' => ['/log/log/index']]
    ];
}
if (User::canRoute('/backup/backup/index')) {
    $appMenu[] = ['label' => Yii::t('app', 'Backups'), 'url' => ['/backup/backup/index']];
}
$appMenu[] = ['label' => Yii::t('app', 'Companies'), 'url' => ['/sale/company']];
$appMenu[] = ['label' => Yii::t('app', 'Points of Sale'), 'url' => ['/sale/point-of-sale']];
$appMenu[] = ['label' => Yii::t('app', 'Billing Config'), 'url' => ['/sale/company-has-billing']];

$appMenu[] = '<li class="divider"></li>';
$appMenu[] = ['label' => Yii::t('app', 'Mailing Configuration'), 'url' => ['/mailing/email-transport/index']];
$appMenu[] = '<li class="dropdown-header">' . Yii::t('app', 'Configuration') . '</li>';


$config = array_merge($appMenu, \app\modules\config\components\Menu::items());

if (Yii::$app->user->isSuperadmin) {
    $config[] = '<li class="divider"></li>';
    $config[] = ['label' => Yii::t('app', 'Config Categories'), 'url' => ['/config/category']];
    $config[] = ['label' => Yii::t('app', 'Config Items'), 'url' => ['/config/item']];
}

//App
if(User::hasRole('admin')) {
    $items[] = ['label' => Yii::t('app', 'Application'), 'items' => $config];
}



//Westnet
if (Yii::$app->getModule('westnet')) {
    $items[] = [
        'label' => 'Westnet',
        'items' => [
            ['label'=>Yii::t('partner','Partner'), 'items' => [
                ['label' => Yii::t('partner', 'Partner'), 'url' => ['/partner/partner']],
                ['label' => Yii::t('partner', 'Partner Distribution Models'), 'url' => ['/partner/partner-distribution-model']],
                '<li class="divider"></li>',
                ['label' => Yii::t('partner', 'Liquidation'), 'url' => ['/partner/liquidation']],
                ['label' => Yii::t('partner', 'Liquidations'), 'url' => ['/partner/liquidation/list-liquidation']],
            ]],
            ['label'=>Yii::t('westnet','Servers'), 'url'=>['/westnet/server'], 'visible' => User::canRoute(['/westnet/server/index'])],
            ['label'=>Yii::t('westnet','Nodes'), 'url'=>['/westnet/node'], 'visible' => User::canRoute(['/westnet/node/index'])],
            ['label'=>Yii::t('westnet','Vendors'), 'url'=>['/westnet/vendor'], 'visible' => User::canRoute(['/westnet/vendor/index'])],
            //'<li class="divider"></li>',
            ['label'=>Yii::t('westnet','Assigned IPs'), 'url'=>['/westnet/node/assigned-ip']],
            ['label'=>Yii::t('westnet','Networks'), 'url'=>['/westnet/ip-range/index']],
            ['label'=>Yii::t('westnet','Access Point'), 'url'=>['/westnet/access-point/index']],
            [
                'label' => Yii::t('westnet', 'Empty ADS not used'),
                'url' => ['/westnet/empty-ads/index']
            ],
            [
                'label' => Yii::t('app', 'Create Empty ADS'),
                'url' => ['/westnet/ads/print-empty-ads']
            ],
            '<li class="divider"></li>',
            [
                'label' => Yii::t('app', 'Ecopagos'),
                'visible' => Yii::$app->user->isSuperadmin
            ],
            [
                'label' => Yii::t('app', 'Ecopagos'), 'url' => ['/westnet/ecopagos/ecopago'], 'visible' => User::canRoute(['/westnet/ecopagos/ecopago/index'])
            ],
            [
                'label' => Yii::t('app', 'Cashiers'), 'url' => ['/westnet/ecopagos/cashier'], 'visible' => User::canRoute(['/westnet/ecopagos/cashier/index'])
            ],
            [
                'label' => Yii::t('app', 'Collectors'), 'url' => ['/westnet/ecopagos/collector'], 'visible' => User::canRoute(['/westnet/ecopagos/collector/index'])
            ],
            '<li class="divider"></li>',
            [
                'label' => Yii::t('app', 'Payouts in Ecopagos'), 'url' => ['/westnet/ecopagos/payout'], 'visible' => User::canRoute(['/westnet/ecopagos/payout'])
            ],
            //'<li class="divider"></li>',
            [
                'label' => Yii::t('app', 'Batch closures'), 'url' => ['/westnet/ecopagos/batch-closure'], 'visible' => User::canRoute(['/westnet/ecopagos/batch-closure'])
            ],
            [
                'label' => Yii::t('app', 'Daily closures'), 'url' => ['/westnet/ecopagos/daily-closure'], 'visible' => User::canRoute(['/westnet/ecopagos/daily-closure'])
            ],
            '<li class="divider"></li>',
            [
                'label' => Yii::t('app', 'Mobile App failed registers'), 'url' => ['/mobileapp/v1/app-failed-register/index'], 'visible' => true
            ],
            '<li class="divider"></li>',
            [
                'label' => Yii::t('app', 'Notifications'), 'url' => ['/westnet/notifications/notification']
            ],
            [
                'label' => Yii::t('app', 'Transports'), 'url' => ['/westnet/notifications/transport'], 'visible' => Yii::$app->user->isSuperadmin
            ],
            '<li class="divider"></li>',
            ['label' => Yii::t('app', 'Integratech'), 'visible' => Yii::$app->user->isSuperadmin],
            [
                'label' =>  NotificationsModule::t('app', 'Integratech sms filters'), 'url' => ['/westnet/notifications/integratech-sms-filter'], 'visible' => Yii::$app->user->isSuperadmin
            ],
            [
                'label' => NotificationsModule::t('app', 'Integratech received sms'), 'url' => ['/westnet/notifications/integratech-received-sms'], 'visible' => Yii::$app->user->isSuperadmin
            ],
            '<li class="divider"></li>',
            ['label' => Yii::t('app', 'Infobip'), 'visible' => Yii::$app->user->isSuperadmin],
            [
                'label' =>  NotificationsModule::t('app', 'Integratech sms filters'), 'url' => ['/westnet/notifications/integratech-sms-filter'], 'visible' => Yii::$app->user->isSuperadmin
            ],
            [
                'label' => Yii::t('app', 'Infobip sended sms'), 'url' => ['/westnet/notifications/infobip/default/sended-messages']
            ],
            [
                'label' => Yii::t('app', 'Infobip received sms'), 'url' => ['/westnet/notifications/infobip/default/index'], 'visible' => Yii::$app->user->isSuperadmin
            ],
            '<li class="divider"></li>',
            ['label' => Yii::t('app', 'Batch Process'), 'visible' => Yii::$app->user->isSuperadmin],
            ['label' => Yii::t('westnet', 'Assign Discount to Customers'), 'url' => ['/westnet/batch/discount-to-customer']],
            ['label' => Yii::t('westnet', 'Assign Plan to Customers'), 'url' => ['/westnet/batch/plans-to-customer']],
            ['label' => Yii::t('westnet', 'Assign Company to Customers'), 'url' => ['/westnet/batch/company-to-customer']],
        ],
    ];
}

$items[] = [
    'label' => Yii::t('app','Help'),
    'items' => [
        ['label' => Yii::t('app','Instructive'), 'url' => ['/instructive/instructive/index']],
        '<li class="divider"></li>',
        ['label' => Yii::t('app','Instructive Category'), 'url' => ['/instructive/instructive-category/index']]
    ]
];

//Tickets
if (Yii::$app->params['ticket_enabled']) {
    $items[] = [
        'label' => 'Tickets',
        'items' => [
            ['label' => Yii::t('app', 'Tickets'), 'url' => ['/ticket/ticket/open-tickets']],
            ['label' => Yii::t('app', 'Cashier Manage Tickets'), 'url' => ['/ticket/ticket/collection-tickets']],
            ['label' => Yii::t('app', 'Installations Manage Tickets'), 'url' => ['/ticket/ticket/installations-tickets']],
            ['label' => Yii::t('app', 'Mobile app data edition tickets'), 'url' => ['/ticket/ticket/contact-edition-tickets']],
//            ['label' => Yii::t('app', 'Mobile app registration tickets'), 'url' => ['/ticket/ticket/installations-tickets']],
            '<li class="divider"></li>',
            ['label' => Yii::t('app', 'Create Ticket'), 'url' => ['/ticket/ticket/create']],
            '<li class="divider"></li>',
            ['label' => Yii::t('app', 'Customers with open tickets'), 'url' => ['/ticket/ticket/list']],
            '<li class="divider"></li>',
            ['label' => Yii::t('app', 'Generated actions'), 'url' => ['/ticket/action']],
            ['label' => Yii::t('app', 'Ticket Statuses'), 'url' => ['/ticket/status']],
            ['label' => Yii::t('app', 'Ticket Colors'), 'url' => ['/ticket/color']],
            ['label' => Yii::t('app', 'Schema'), 'url' => ['/ticket/schema']],
            ['label' => Yii::t('app', 'Ticket Categories'), 'url' => ['/ticket/category']],
        ],
    ];
}

//Agenda
if (Yii::$app->params['agenda_enabled']) {

    echo Task::widget();
    $array_items = [
        ['label' => Yii::t('app', 'My agenda'), 'url' => ['/agenda']],
        ['label' => Yii::t('app', 'Tasks'), 'url' => ['/agenda/task']],
        '<li class="divider"></li>',
        ['label' => Yii::t('app', 'Create Task'), 'url' => ['/agenda'],
            'linkOptions' => [
                'data-task' => 'create',
            ],
        ],
        ['label' => Yii::t('app', 'Task Categories'), 'url' => ['/agenda/category'], 'visible'=> User::canRoute('/agenda/category/*')],
        ['label' => Yii::t('app', 'Task Types'), 'url' => ['/agenda/task-type'], 'visible'=> User::canRoute('/agenda/task-type/*')],
        ['label' => Yii::t('app', 'Task Statuses'), 'url' => ['/agenda/status'],'visible'=> User::canRoute('/agenda/status/*')],
        ['label' => Yii::t('app', 'Event Types'), 'url' => ['/agenda/task-type'],'visible'=> User::canRoute('/agenda/task-type/*')],
    ];

    $items[] = [
        'label' => 'Agenda',
        'items' => $array_items,
    ];

    if (User::canRoute('/agenda')) {

        echo Notification::widget();

        $items[] = [
            'linkOptions' => [
                'data-agenda' => 'notifications',
                'data-notifications' => 'show'
            ],
            'label' => '<div class="agenda-notifications">'
                . '<span class="icon glyphicon glyphicon-comment">'
                . '</span><span data-notification-count="0" class="badge"></span>'
                . '</div>',
            'visible' => !Yii::$app->user->isGuest
        ];
    }
}

//Usuarios
$items[] = [
    'label' => 'Usuarios',
    'items' => webvimark\modules\UserManagement\UserManagementModule::menuItems()
];

// Cuenta de usuario
$items[] = [
    'label' => '<span class="glyphicon glyphicon-user"></span> (' . Yii::$app->user->identity->username . ')',
    'items' => [
    ['label' => Yii::$app->user->identity->username, 'visible' => !Yii::$app->user->isGuest],
    '<li class="divider"></li>',
    ['label' => Yii::t('modules/user-management/front', 'Login'), 'url' => ['/user-management/auth/login'], 'visible' => Yii::$app->user->isGuest],
    ['label' => Yii::t('modules/user-management/front', 'Logout'), 'url' => ['/user-management/auth/logout'], 'visible' => !Yii::$app->user->isGuest],
    ['label' => Yii::t('modules/user-management/front', 'Registration'), 'url' => ['/user-management/auth/registration'], 'visible' => Yii::$app->user->isGuest],
    ['label' => Yii::t('modules/user-management/front', 'Change own password'), 'url' => ['/user-management/auth/change-own-password']],
    ['label' => Yii::t('modules/user-management/front', 'Password recovery'), 'url' => ['/user-management/auth/password-recovery']],
    ['label' => Yii::t('modules/user-management/front', 'E-mail confirmation'), 'url' => ['/user-management/auth/confirm-email']],
    ],
];
?>


<nav id="main-menu" class="navbar navbar-inverse <?= YII_ENV == 'test' ? '' : 'navbar-fixed-top' ?>">

    <div class="container-fluid">

        <div class="navbar-header" id="narrow-navbar">
            <button type="button" class="navbar-toggle collapsed pull-left" data-toggle="collapse" data-target="#wide-navbar" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>


            <a class="navbar-brand" href="<?= Yii::$app->homeUrl; ?>"><?php echo Yii::$app->params['web_title'] ?></a>

            <?php
            echo Nav::widget([
                'options' => ['class' => ' navbar-nav navbar-right pull-right no-margin display-navbar-breakpoint navbar-links-responsive '],
                'items' => $alwaysVisibleItems,
                'encodeLabels' => false,
                'activateParents' => true
            ]);
            ?>

            
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="wide-navbar">

            <?php
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => array_merge($alwaysVisibleItems, $items),
                'encodeLabels' => false,
                'activateParents' => true
            ]);
            ?>
        </div>
    </div>
</nav>
