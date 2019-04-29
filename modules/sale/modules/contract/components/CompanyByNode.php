<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 13/07/18
 * Time: 14:47
 */

namespace app\modules\sale\modules\contract\components;


use app\modules\sale\models\BillType;
use app\modules\sale\models\Company;
use app\modules\sale\models\CompanyHasBilling;
use app\modules\sale\models\Customer;
use app\modules\sale\models\TaxCondition;
use app\modules\westnet\models\Node;
use app\modules\westnet\models\NodeHasCompanies;

/**
 * Class CompanyByNode
 *
 * Helper para determinar que company de facturacion le corresponde segun el nodo y los datos del customer.
 *
 * Como los tax_condition no se deberian de  modificar, voy a suponer los siguientes valores:
 *  - 1 IVA Inscripto
 *  - 2 IVA No inscripto
 *  - 3 Consumidor Final
 *  - 4 Exento
 *  - 5 Monotributista
 *
 * @package app\modules\sale\modules\contract\components
 */
class CompanyByNode
{

    /**
     * Setea la empresa en base al nodo
     *
     * @param Node $node
     * @param Customer $customer
     * @return Company|null|void
     */
    public static function setCompanyToCustomer(Node $node, Customer $customer)
    {
        $billtypes = [
            '1'     => 'A',
            '6'     => 'B',
            '11'    => 'C',
            '10'    => 'X',
        ];

        // Pongo el 10 en caso de que sea por defecto
        $billType = ( ($customer->needs_bill || (!$customer->needs_bill && !$node->has_ecopago_close)) ? ($customer->tax_condition_id == 1 ? 1 : 6 ) : 10 );

        /** @var CompanyHasBilling $chb */
        $chb = CompanyHasBilling::find()->leftJoin('bill_type bt', 'company_has_billing.bill_type_id = bt.bill_type_id')->where(["parent_company_id"=>$customer->parent_company_id, "bt.code"=> $billType])->one();
        if($chb) {
            $customer->company_id = $chb->company_id;
            $customer->updateAttributes(['company_id']);
            $customer->updatePaymentCode();
        }
    }
}