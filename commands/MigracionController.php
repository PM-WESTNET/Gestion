<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\modules\config\models\Config;
use app\modules\sale\components\BillExpert;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Company;
use Yii;
use yii\console\Controller;
use yii\db\Query;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MigracionController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        set_time_limit(0);
        $credit_id = 14;
        $debit_id = 15;
        $pointOfSale = [];

        foreach(Company::findAll() as $company ) {
            $pointOfSale[$company->company_id] = $company->getPointsOfSale();
        }

        $sql = 'SELECT *
                    FROM (SELECT
                            customer.customer_id,
                            concat(customer.lastname, \' \', customer.name) AS name,
                            customer.phone,
                            customer.code,
                            round(coalesce((SELECT sum(b.total * bt.multiplier) AS amount
                                            FROM bill b LEFT JOIN bill_type bt ON b.bill_type_id = bt.bill_type_id
                                            WHERE b.status = \'closed\' AND b.customer_id = customer.customer_id), 0) -
                                  coalesce((SELECT sum(pi.amount)
                                            FROM payment p LEFT JOIN payment_item pi
                                                ON p.payment_id = pi.payment_id AND pi.payment_method_id NOT IN (SELECT payment_method_id
                                                                                                                 FROM payment_method
                                                                                                                 WHERE type = \'account\')
                                            WHERE p.status <> \'cancelled\' AND p.customer_id = customer.customer_id), 0)) AS saldo,
                            customer.company_id
                          FROM customer
                            LEFT JOIN (SELECT
                                         customer_id,
                                         sum(qty)   AS debt_bills,
                                         sum(qty_2) AS payed_bills
                                       FROM (SELECT
                                               customer_id,
                                               date,
                                               i,
                                               round(amount, 2),
                                               @saldo :=
                                               round(if(customer_id <> @customer_ant AND @customer_ant <> 0, amount, @saldo + amount),
                                                     2)                                                                    AS saldo,
                                               @customer_ant := customer_id,
                                               if((@saldo - (SELECT cc.percentage_tolerance_debt
                                                             FROM customer_class_has_customer cchc
                                                               INNER JOIN (SELECT
                                                                             customer_id,
                                                                             max(date_updated) maxdate
                                                                           FROM customer_class_has_customer
                                                                           GROUP BY customer_id) cchc2
                                                                 ON cchc2.customer_id = cchc.customer_id AND
                                                                    cchc.date_updated = cchc2.maxdate
                                                               LEFT JOIN customer_class cc ON cchc.customer_class_id = cc.customer_class_id
                                                             WHERE cchc.customer_id = a.customer_id)) > 0 AND i = 1, 1, 0) AS qty,
                                               if(@saldo <= 0 AND i = 1, 1, 0)                                             AS qty_2
                                             FROM ((SELECT
                                                      customer_id,
                                                      b.date                   AS date,
                                                      if(bt.multiplier < 0, 0, 1)  AS i,
                                                      sum(b.total * bt.multiplier) AS amount
                                                    FROM bill b
                                                         FORCE INDEX (fk_bill_customer1_idx) LEFT JOIN bill_type bt
                                                        ON b.bill_type_id = bt.bill_type_id
                                                    WHERE b.status = \'closed\'
                                                    GROUP BY b.customer_id, b.bill_id)
                                                   UNION ALL (SELECT
                                                                p.customer_id,
                                                                p.date AS date,
                                                                0          AS i,
                                                                -p.amount
                                                              FROM payment p
                                                              WHERE p.status = \'closed\')) a
                                             ORDER BY customer_id, i, date) a
                                       GROUP BY customer_id) bills ON bills.customer_id = customer.customer_id
                            LEFT JOIN contract ON contract.customer_id = customer.customer_id
                            LEFT JOIN contract_detail ON contract.contract_id = contract_detail.contract_id
                            INNER JOIN customer_class_has_customer cchc ON cchc.customer_id = customer.customer_id
                            INNER JOIN (SELECT
                                          customer_id,
                                          max(date_updated) maxdate
                                        FROM customer_class_has_customer
                                        GROUP BY customer_id) cchc2
                              ON cchc2.customer_id = customer.customer_id AND cchc.date_updated = cchc2.maxdate
                            INNER JOIN customer_category_has_customer ccathc ON ccathc.customer_id = customer.customer_id
                            INNER JOIN (SELECT
                                          customer_id,
                                          max(date_updated) maxdate
                                        FROM customer_category_has_customer
                                        GROUP BY customer_id) ccathc2
                              ON ccathc2.customer_id = customer.customer_id AND ccathc.date_updated = ccathc2.maxdate
                            LEFT JOIN customer_class cc ON cchc.customer_class_id = cc.customer_class_id
                            LEFT JOIN customer_category ccat ON ccathc.customer_category_id = ccat.customer_category_id
                            LEFT JOIN connection ON connection.contract_id = contract.contract_id
                            LEFT JOIN node n ON connection.node_id = n.node_id
                            LEFT JOIN company ON company.company_id = customer.company_id
                          GROUP BY customer.customer_id, customer.name, customer.phone) b
                    WHERE saldo <> 0 and company_id in ( 1,2,3,7);';

        $default_unit_id = Config::getValue('default_unit_id');

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        foreach($result as $row) {
            $bill_type_id = ($row['saldo'] > 0 ? $debit_id : $credit_id );

            /** @var Bill $bill */
            $bill = BillExpert::createBill($bill_type_id);
            $bill->company_id = $row['company_id'];
            $bill->pointOfSale = $pointOfSale[$row['company_id']];;
            $bill->customer_id = $row['customer_id'];
            $bill->date = '2018-02-01';
            $bill->status = 'draft';
            $bill->save(false);

            $bill->addDetail([
                'product_id' => null,
                'unit_id' => $default_unit_id,
                'qty' => 1,
                'type' => null,
                'unit_net_price' => abs($row['saldo']),
                'unit_final_price' => abs($row['saldo']),
                'concept' => ($row['saldo'] < 0 ? 'Credito por migración.' : 'Debito por migración.' ),
                'discount_id' => null,
                'unit_net_discount' => null
            ]);
            $bill->number = $this->getBillNumber($bill_type_id, $bill->company_id);
            $bill->save(false);
            $bill->fillNumber = false;
            $bill->complete();
            $bill->close();

        }

    }

    private function getBillNumber($bill_type_id, $company_id)
    {
        if(array_key_exists($bill_type_id."-".$company_id, $this->numbers)) {
            $lastNumber = $this->numbers[$bill_type_id."-".$company_id] + 1;
        } else {
            $lastNumber = Bill::find()->where([
                    'bill_type_id' => $bill_type_id,
                    'status' => 'closed',
                    'company_id' => $company_id
                ])->max('number') + 1;
        }
        $this->numbers[$bill_type_id."-".$company_id] = $lastNumber;
        return $lastNumber;
    }
}
