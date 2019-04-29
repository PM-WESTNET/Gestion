<?php

namespace app\modules\westnet\commands;

use app\modules\config\models\Config;
use app\modules\sale\models\BillDetail;
use app\modules\sale\models\bills\Bill;
use app\modules\sale\models\TaxRate;
use Yii;
use yii\console\Controller;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\Console;

/**
 * Class ZoneController
 * Se actualizan todas las zonas desde los nodos padres.
 * @package app\commands
 */
class UpdateBillsController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        //Intro
        $this->stdout("Arya[Bill]", Console::BOLD, Console::FG_CYAN);
        $this->stdout(" | Command to update bills.\n", Console::BOLD);
        $this->stdout("Starting...\n", Console::BOLD);

        $this->updateBills();
        $this->stdout("Proccess successfully finished.\n", Console::BOLD, Console::FG_GREEN);
    }

    /**
     * Actualiza el arbol de zonas.
     *
     * @param int $parent_id
     * @param int $left
     * @return int
     * @internal param int|null $parent_account_id
     */
    private function updateBills()
    {
        /** @var ActiveQuery $query */
        $query = Bill::find()
            ->leftJoin('bill_type bt', 'bill.bill_type_id = bt.bill_type_id')
            ->where('bill.class != -1 and bt.multiplier >0 ')
        ;

        $cantidadTotal = $query->count();
        $paginas = ceil($cantidadTotal/100);

        $this->stdout('Total a Procesar:' . $cantidadTotal."\n");

        $taxRatePct = 1 + TaxRate::findOne(['code'=>Config::getValue('default_tax_rate_code') ])->pct;
        for($r=0; $r<$paginas; $r++) {
            $bills  = $query
                ->offset($r*100)
                ->limit(100)
                ->all()
            ;
            $this->stdout('Pagina:' . ($r+1)."\n");

            foreach ($bills as $bill) {
                /** @var BillDetail $detail */
                foreach ($bill->billDetails as $detail) {
                    if($detail->unit_net_price == $detail->unit_final_price){
                        $detail->unit_final_price = $detail->unit_net_price * $taxRatePct;
                        $detail->update(false);
                    }

                }
            }
        }
    }

    /**
     * Actualiza los montos de bill vacios en base a bill_detail
     *
     * @return int
     * @internal param int|null $parent_account_id
     */
    public function actionUpdateBillsWithOutAmount()
    {
        // select * from bill where status = 'closed' and amount = 0
        /** @var ActiveQuery $query */
        $query = Bill::find()
            ->where(['status'=>'closed', 'amount'=>0, new Expression('date >= \'2017-12-01\'')])
        ;

        $this->stdout('Procesando:'."\n");

        $bills = $query->all();
        foreach ($bills as $bill) {

            echo $bill->bill_id . " - - " . $bill->amount . " " . $bill->total . " - " . $bill->taxes . "\n";
            $bill->updateAmounts();
            echo $bill->bill_id . " - - " . $bill->amount . " " . $bill->total . " - " . $bill->taxes . "\n";

            $amount = 0;
            $total = 0;
            $taxes = 0;
            if($bill->amount) {
                $bill->update(false);
            }
        }
    }
}