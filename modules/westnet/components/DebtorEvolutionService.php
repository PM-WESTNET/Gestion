<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 5/09/17
 * Time: 16:24
 */

namespace app\modules\westnet\components;


use app\modules\sale\models\search\CustomerSearch;
use app\modules\westnet\models\DebtEvolution;
use yii\db\Expression;
use yii\db\Query;

class DebtorEvolutionService
{
    public function process(\DateTime $date)
    {
        try {
            $search = new CustomerSearch();
            $search->toDate = $date->format('Y-m-d');
            $search->amount_due = 0;

            $query = $search->buildDebtorsQuery([]);
            $query->select(['*']);

            $masterQuery = new Query();

            $masterQuery->select([
                new Expression('SUM(CASE WHEN debt_bills = 1 THEN 1 ELSE 0 END) as invoice_1'),
                new Expression('SUM(CASE WHEN debt_bills = 2 THEN 1 ELSE 0 END) as invoice_2'),
                new Expression('SUM(CASE WHEN debt_bills = 3 THEN 1 ELSE 0 END) as invoice_3'),
                new Expression('SUM(CASE WHEN debt_bills = 4 THEN 1 ELSE 0 END) as invoice_4'),
                new Expression('SUM(CASE WHEN debt_bills = 5 THEN 1 ELSE 0 END) as invoice_5'),
                new Expression('SUM(CASE WHEN debt_bills = 6 THEN 1 ELSE 0 END) as invoice_6'),
                new Expression('SUM(CASE WHEN debt_bills = 7 THEN 1 ELSE 0 END) as invoice_7'),
                new Expression('SUM(CASE WHEN debt_bills = 8 THEN 1 ELSE 0 END) as invoice_8'),
                new Expression('SUM(CASE WHEN debt_bills = 9 THEN 1 ELSE 0 END) as invoice_9'),
                new Expression('SUM(CASE WHEN debt_bills = 10 THEN 1 ELSE 0 END) as invoice_10'),
                new Expression('SUM(CASE WHEN debt_bills > 10 THEN 1 ELSE 0 END) as invoice_x')
            ])->from(['b' => $query]);

            $all = $masterQuery->all();
            if ($all) {
                $debtevo = new DebtEvolution();
                $debtevo->period = $date->format('Y-m-d');
                $debtevo->invoice_1 = $all[0]['invoice_1'];
                $debtevo->invoice_2 = $all[0]['invoice_2'];
                $debtevo->invoice_3 = $all[0]['invoice_3'];
                $debtevo->invoice_4 = $all[0]['invoice_4'];
                $debtevo->invoice_5 = $all[0]['invoice_5'];
                $debtevo->invoice_6 = $all[0]['invoice_6'];
                $debtevo->invoice_7 = $all[0]['invoice_7'];
                $debtevo->invoice_8 = $all[0]['invoice_8'];
                $debtevo->invoice_9 = $all[0]['invoice_9'];
                $debtevo->invoice_10 = $all[0]['invoice_10'];
                $debtevo->invoice_x = $all[0]['invoice_x'];
                $debtevo->save(false);
            }
            return true;
        }catch(\Exception $ex) {
            throw $ex;
        }
    }
}