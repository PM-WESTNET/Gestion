<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 03/10/19
 * Time: 12:18
 */
namespace app\commands;

use app\modules\checkout\models\Payment;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerLog;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\sale\modules\contract\models\Plan;
use app\modules\sale\modules\contract\models\ProgrammedPlanChange;
use yii\console\Controller;
use app\modules\checkout\models\search\PaymentSearch;
use Yii;

class ProgrammedPlanChangeController extends Controller
{

    /**
     * Aplica los cambios de velocidad programados
     */
    public function actionApplyProgrammedPlanChanges()
    {

        try {
            $today_first_hour = (new \DateTime('now'))->format('Y-m-d 00:00:00');
            $today_last_hour = (new \DateTime('now'))->format('Y-m-d 23:59:59');
            $timestamp_today = (new \DateTime($today_first_hour))->getTimestamp();
            $timestamp_today_last_hour = (new \DateTime($today_last_hour))->getTimestamp();


            $programmed_plan_changes = ProgrammedPlanChange::find()
                ->where(['applied' => 0])
                ->andWhere(['>=','date', $timestamp_today])
                ->andWhere(['<=', 'date', $timestamp_today_last_hour])
                ->all();

            echo 'Cambios Encontrados: ' . count($programmed_plan_changes);
            echo "\n";

            foreach ($programmed_plan_changes as $plan_change) {
                $contract = Contract::findOne($plan_change->contract_id);
                $actual_contract_detail_plan = ContractDetail::find()
                    ->leftJoin('product', 'product.product_id = contract_detail.product_id')
                    ->where(['product.type' => 'plan'])
                    ->andWhere(['contract_detail.contract_id' => $plan_change->contract_id])
                    ->one();

                $old_plan = $actual_contract_detail_plan->product;
                $new_plan = Plan::findOne($plan_change->product_id);

                if ($actual_contract_detail_plan) {
                    echo 'Cambio a '. $contract->customer->fullName . ' de ' . $old_plan->name . ' a ' . $new_plan->name;
                    echo "\n";
                    $old_contract_detail = clone($actual_contract_detail_plan);
                    $old_contract_detail->to_date = (new \DateTime('now'))->modify('-1 day')->format('d-m-Y');
                    if($old_contract_detail->createLog()) {
                        $customerLog = new CustomerLog();
                        $customerLog->createUpdateLog($contract->customer_id, 'Plan', $old_plan->name, $new_plan->name, 'Contract', $plan_change->contract_id);
                        $actual_contract_detail_plan->product_id = $plan_change->product_id;
                        $actual_contract_detail_plan->from_date = (new \DateTime('now'))->format('d-m-Y');
                        $actual_contract_detail_plan->applied = 0;
                        $actual_contract_detail_plan->save(false, ['from_date', 'status', 'product_id', 'applied']);
                        $plan_change->updateAttributes(['applied' => 1]);
                    } else {
                        \Yii::info('Falló al crear el contract log de '.$actual_contract_detail_plan->contract_detail_id, 'cambio-de-velocidad-programada');
                    }
                } else {
                    \Yii::info('Falló al encontrar el contract detail con plan del contract id '.$plan_change, 'cambio-de-velocidad-programada');
                }
            }
        } catch (\Exception $ex) {
            echo "error: " . $ex->getMessage();
            echo "\n";

            \Yii::info('Falla el proceso: '.$ex->getTraceAsString(), 'cambio-de-velocidad-programada');
        }
    }
}