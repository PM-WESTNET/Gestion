<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 04/01/19
 * Time: 11:58
 */
namespace app\commands;

use app\modules\accounting\models\AccountingPeriod;
use app\modules\mobileapp\v1\models\Customer;
use app\modules\provider\models\Provider;
use app\modules\provider\models\ProviderBill;
use app\modules\sale\models\Bill;
use app\modules\sale\models\BillDetail;
use app\modules\sale\models\BillType;
use app\modules\sale\models\Company;
use app\modules\sale\models\PointOfSale;
use app\modules\sale\models\TaxCondition;
use Yii;
use app\modules\sale\components\BillExpert;
use app\modules\config\models\Config;
use yii\db\Query;
use app\modules\accounting\models\AccountMovement;
use app\modules\accounting\models\AccountMovementItem;

class DivisionController extends \yii\console\Controller
{

    public function actionCreateInitialCustomerBalance()
    {
        $descuento = BillType::findOne(['name' => 'Descuento']);
        $adicional = BillType::findOne(['name' => 'Adicional']);
        $balances = $this->getBalances();
        $points_of_sale = $this->getPointsOfSale();
        $company_bill_type_added = $this->addCompanyBillTypes([$descuento, $adicional]);
        $tax_condition_bill_type_added = $this->addTaxConditionBillTypes([$descuento, $adicional]);

        foreach ($balances as $balance) {
            $customer = Customer::findOne($balance['customer_id']);
            $company_id = $customer->company->company_id;
            $bill_type_id = $balance['saldo'] < 0 ? $descuento->bill_type_id : $adicional->bill_type_id;
            $this->createBill($bill_type_id, $points_of_sale[$company_id], $customer, $balance['saldo']);
        }

        $this->removeTaxConditionBillTypes($tax_condition_bill_type_added);
        $this->removeCompanyBillTypes($company_bill_type_added);
        return $balances;
    }

    public function actionCreateInitialProviderBalance()
    {
        $descuento = BillType::findOne(['name' => 'Descuento']);
        $adicional = BillType::findOne(['name' => 'Adicional']);
        $balances = $this->getProviderBalances();
        $company_bill_type_added = $this->addCompanyBillTypes([$descuento, $adicional]);
        $tax_condition_bill_type_added = $this->addTaxConditionBillTypes([$descuento, $adicional]);

        foreach ($balances as $balance) {
            $provider = Provider::findOne($balance['provider_id']);
            $company = Company::findOne($balance['company_id']);
            $bill_type_id = $balance['saldo'] > 0 ? $descuento->bill_type_id : $adicional->bill_type_id;
            $this->createProviderBill($bill_type_id, $provider, $balance['saldo'], $company);
        }

        $this->removeTaxConditionBillTypes($tax_condition_bill_type_added);
        $this->removeCompanyBillTypes($company_bill_type_added);
        return $balances;
    }

    public function actionCreateMovementAccount()
    {
        $balances = $this->getAccountMovementBalances();
        $accounting_period = $this->createAccountingPeriod();

        foreach ($balances as $balance) {
            $company = Company::findOne($balance['company_id']);
            if(!$company instanceof Company) {
                $company = Company::findOne(['name' => 'CUPÓN DE PAGO']);
            }

            $partner_distribution_model_id = $company->partner_distribution_model_id;
            $account_id = $balance['account_id'];
            $this->createAccountMovement($company->company_id, $partner_distribution_model_id,  $accounting_period->accounting_period_id, $account_id, $balance['debit'], $balance['credit']);
        }
    }

    public function createAccountingPeriod()
    {
        $accounting_period = new AccountingPeriod([
            'name' => '2018',
            'date_from' => '01-01-2018',
            'date_to' => '31-12-2018',
            'number' => 8,
            'status' => 'closed'
        ]);

        $accounting_period->save();

        return $accounting_period;
    }

    public function createAccountMovement($company_id, $partner_distribution_model_id,  $accounting_period_id, $account_id,  $debit, $credit)
    {
        $movement = new AccountMovement();
        $movement->date = '01-08-2018';
        $movement->time = '00:00:00';
        $movement->description = 'Saldo inicial';
        $movement->status = 'closed';
        $movement->company_id = $company_id;
        $movement->accounting_period_id = $accounting_period_id;
        $movement->partner_distribution_model_id = $partner_distribution_model_id;
        $movement->save();

        $accountMovementItem = new AccountMovementItem();
        $accountMovementItem->account_id = $account_id;
        $accountMovementItem->account_movement_id = $movement->account_movement_id;
        $accountMovementItem->debit = $debit;
        $accountMovementItem->credit = $credit;
        $accountMovementItem->status = 'closed';

        $accountMovementItem->save();
        //var_dump($movement->account_movement_id);
    }

    /**
     * @param $bill_types
     * @return array
     * @throws \yii\db\Exception
     * Vuelve la configuración de bill type en tax condition a su estado anterior.
     */
    private function removeTaxConditionBillTypes($tax_condition_bill_type_added)
    {
        foreach ($tax_condition_bill_type_added as $tcbta) {
            Yii::$app->db->createCommand()->delete('tax_condition_has_bill_type', [
                'tax_condition_id' =>  $tcbta['tax_condition_id'],
                'bill_type_id' => $tcbta['bill_type_id']
            ])->execute();
        }
    }

    /**
     * @param $bill_types
     * @return array
     * @throws \yii\db\Exception
     * Vuelve la configuración de bill type en company a su estado anterior.
     */
    private function removeCompanyBillTypes($company_bill_type_added)
    {
        foreach ($company_bill_type_added as $cbta) {
            Yii::$app->db->createCommand()->delete('company_has_bill_type', [
                'company_id' =>  $cbta['company_id'],
                'bill_type_id' => $cbta['bill_type_id']
            ])->execute();
        }
    }

    /**
     * @param $bill_types
     * @return array
     * @throws \yii\db\Exception
     * Crea la relacion de el bill type descuento / adicional a todas los tax_condition.
     * Devuelve un array con las relaciones que ha creado, a fin de volver a la normalidad la configuración
     * luego de terminado el proceso.
     */
    private function addTaxConditionBillTypes($bill_types)
    {
        $tax_conditions = TaxCondition::find()->all();
        $tax_conditions_bill_type_added = [];
        foreach ($bill_types as $bill_type){
            foreach ($tax_conditions as $tax_condition) {
                $exists = $tax_condition->getBillTypes()->where(['bill_type_id' => $bill_type->bill_type_id])->exists();
                if(!$exists) {
                    Yii::$app->db->createCommand()->insert('tax_condition_has_bill_type', [
                        'tax_condition_id' => $tax_condition->tax_condition_id,
                        'bill_type_id' => $bill_type->bill_type_id
                    ])->execute();
                    array_push($tax_conditions_bill_type_added, ['tax_condition_id' => $tax_condition->tax_condition_id, 'bill_type_id' => $bill_type->bill_type_id]);
                }
            }
        }
        return $tax_conditions_bill_type_added;
    }

    /**
     * @param $bill_types
     * @return array
     * @throws \yii\db\Exception
     * Crea la relacion de el bill type descuento / adicional a todas las empresas habilitadas.
     * Devuelve un array con las relaciones que ha creado, a fin de volver a la normalidad la configuración
     * luego de terminado el proceso.
     */
    private function addCompanyBillTypes($bill_types)
    {
        $companies = Company::find()->where(['status' => 'enabled'])->all();
        $company_bill_type_added = [];
        foreach ($bill_types as $bill_type){
            foreach ($companies as $company) {
                $exists = (new Query())
                    ->select('*')
                    ->from('company_has_bill_type')
                    ->where(['company_id' => $company->company_id])
                    ->andWhere(['bill_type_id' => $bill_type->bill_type_id])
                    ->exists();
                if(!$exists) {
                    Yii::$app->db->createCommand()->insert('company_has_bill_type', [
                        'company_id' => $company->company_id,
                        'bill_type_id' => $bill_type->bill_type_id
                    ])->execute();
                    array_push($company_bill_type_added, ['company_id' => $company->company_id, 'bill_type_id' => $bill_type->bill_type_id]);
                }
            }
        }

        return $company_bill_type_added;
    }
    /**
     * @return array
     * Devuelve un array con  [company_id => point_of_sale_id]
     */
    public function getPointsOfSale()
    {
        $points_of_sale = [];
        $companies = Company::find()->all();

        foreach ($companies as $company) {
            $point_of_sale = $this->getPointOfSale($company->company_id);
            $points_of_sale[$company->company_id] = $point_of_sale->point_of_sale_id;
        }

        return $points_of_sale;
    }

    /**
     * @param $company_id
     * @return PointOfSale
     * Devuelve un punto de venta de saldos iniciales. Si el punto de venta para el saldo inicial no existe, lo crea.
     */
    private function getPointOfSale($company_id)
    {
        $company = Company::findOne($company_id);
        $existent_point_of_sale = $company->getPointsOfSale()->where(['number' => 15])->one();

        if ($existent_point_of_sale) {
            return $existent_point_of_sale;
        } else {
            $point_of_sale = new PointOfSale([
                'name' => 'Saldo inicial',
                'number' => 15,
                'status' => 'enabled',
                'description' => 'Punto de venta para saldos iniciales',
                'company_id' => $company_id,
                'default' => 0,
                'electronic_billing' => 0
            ]);

            return $point_of_sale;
        }
    }

    /**
     * @param $bill_type_id
     * @param $point_of_sale_id
     * @param $customer
     * @param $amount
     * @param $company_id
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\HttpException
     * Crea un comprobante
     */
    public function createBill($bill_type_id, $point_of_sale_id, $customer, $amount)
    {
        $default_unit_id = Config::getValue('default_unit_id');

        $bill = BillExpert::createBill($bill_type_id);
        $bill->company_id = $customer->company->company_id;
        $bill->point_of_sale_id = $point_of_sale_id;
        $bill->customer_id = $customer->customer_id;
        $bill->date = '2018-08-01';
        $bill->status = 'draft';

        $bill->save(false);

        $a = $bill->addDetail([
            'product_id' => null,
            'unit_id' => $default_unit_id,
            'qty' => 1,
            'type' => null,
            'unit_net_price' => abs($amount),
            'unit_final_price' => abs($amount),
            'concept' => ($amount < 0 ? 'Credito inicial.' : 'Debito inicial.' ),
            'discount_id' => null,
            'unit_net_discount' => null,
        ]);

        $bill->number = $this->getBillNumber($bill_type_id, $bill->company_id);
        $bill->fillNumber = false;

        $bill->status = 'closed';
        $bill->save(false);

       // var_dump('customer_id '.$customer->customer_id .' - '.$amount);
    }

    public function createProviderBill($bill_type_id, $provider, $amount, $company)
    {
        /** @var ProviderBill $bill */
        $bill = new ProviderBill();
        $bill->bill_type_id = $bill_type_id;
        $bill->date = '2018-08-01';
        $bill->company_id = $company->company_id;
        $bill->provider_id = $provider->provider_id;
        $bill->partner_distribution_model_id = $company->partner_distribution_model_id;
        $bill->net = abs($amount);
        $bill->description = ($amount < 0 ? 'Credito inicial.' : 'Debito inicial.' );
        $bill->status = 'draft';
        $bill->save();

        $bill->addItem([
            'provider_bill_id'=> $bill->provider_bill_id,
            'account_id'=> null,
            'amount'=> abs($amount),
            'description'=> ($amount < 0 ? 'Credito inicial.' : 'Debito inicial.' ),
        ]);

        //var_dump('provider_bill_id: '. $bill->provider_bill_id . ' - ' . $amount);
        $bill->close();
    }

    private function getProviderBillNumber($bill_type_id, $company_id)
    {
        $lastNumber = Bill::find()->where([
                'bill_type_id' => $bill_type_id,
                'status' => 'closed',
                'company_id' => $company_id])
                ->max('number') + 1;

        return $lastNumber;
    }

    private function getBillNumber($bill_type_id, $company_id)
    {
        $lastNumber = Bill::find()->where([
            'bill_type_id' => $bill_type_id,
            'status' => 'closed',
            'company_id' => $company_id])
                ->max('number') + 1;

        return $lastNumber;
    }

    public function getBalances()
    {
        $balances = Yii::$app->db->createCommand("
            select customer_id, round(sum(cobrado) - sum(facturado),2) as saldo
            from (
              SELECT b.customer_id, round(sum(b.total * bt.multiplier),2) as facturado, 0 as cobrado
              FROM bill b
                LEFT JOIN company ON company.company_id = b.company_id
                LEFT JOIN bill_type bt ON b.bill_type_id = bt.bill_type_id
              WHERE b.date <= '2018-07-31'
              AND company.status = 'enabled'
              GROUP BY b.customer_id
              union all
              SELECT p.customer_id, 0 as facturado, round(sum(coalesce(i.amount, i.amount, p.amount)),2) as cobrado
              FROM payment p
                  LEFT JOIN company ON company.company_id = p.company_id
                  LEFT JOIN payment_item i on p.payment_id = i.payment_id
              WHERE p.date <= '2018-07-31'
              AND company.status = 'enabled'
              AND p.status = 'closed'
              GROUP BY p.customer_id
            )c 
            WHERE customer_id IS NOT NULL
            AND customer_id <> 0
            group by customer_id
            having round(sum(cobrado) - sum(facturado)) <> 0
        ;")->queryAll();

        return $balances;
    }

    public function getProviderBalances() {
        $provider_balances = Yii::$app->db->createCommand("
        select company_id, provider_id, ROUND(sum(pagado) - sum(facturado),2) as saldo
        from (
             select b.company_id, b.provider_id, round(sum(b.total * bt.multiplier),2) as facturado, 0 as pagado
          from provider_bill b left join bill_type bt on b.bill_type_id = bt.bill_type_id
          WHERE b.date <= '2018-07-31'
          group by b.company_id, b.provider_id
        union all
        select p.company_id, p.provider_id, 0 as facturado, round(sum(i.amount),2) as pagado
        FROM provider_payment p
        left join provider_payment_item i
          on p.provider_payment_id = i.provider_payment_id
          WHERE p.date <= '2018-07-31'
        group by p.company_id, p.provider_id
        ) c
        GROUP BY  provider_id
        having  ROUND(sum(pagado) - sum(facturado),2) <> 0
        ;
        ")->queryAll();

        return $provider_balances;
    }

    public function getAccountMovementBalances()
    {
        $account_movements = Yii::$app->db->createCommand("
            SELECT am.company_id, a3.account_id,  SUM(a.debit) as debit, SUM(a.credit) as credit
            FROM account_movement am
            LEFT JOIN account_movement_item a ON am.account_movement_id = a.account_movement_id
            LEFT JOIN account a3 ON a.account_id = a3.account_id
            LEFT JOIN account_movement_relation a2 ON am.account_movement_id = a2.account_movement_id
            where am.date <= '2018-07-31'
            group by am.company_id, a3.account_id
        ")->queryAll();

        return $account_movements;
    }
}
