<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 12/05/16
 * Time: 14:41
 */

namespace app\modules\partner\components;


use app\modules\accounting\models\Account;
use app\modules\accounting\models\AccountingPeriod;
use app\modules\accounting\models\AccountMovement;
use app\modules\accounting\models\AccountMovementItem;
use app\modules\config\models\Config;
use app\modules\partner\models\PartnerDistributionModel;
use app\modules\partner\models\PartnerLiquidation;
use app\modules\partner\models\PartnerLiquidationItem;
use app\modules\partner\models\search\PartnerLiquidationSearch;
use Yii;
use yii\db\Expression;
use yii\db\Query;

class Liquidation
{
    /**
     * @var array
     */
    private $messages;


    /**
     * PartnerLiquidation constructor.
     */
    public function __construct()
    {
        $this->messages = array();
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param array $messages
     * @return PartnerLiquidation
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @return bool|void
     */
    public function liquidate()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $search = new PartnerLiquidationSearch();

            $accounts = $search->searchAccounts([])->all();
            foreach ($accounts as $account) {
                $pl = new PartnerLiquidation();
                $pl->partner_distribution_model_has_partner_id = $account['partner_distribution_model_has_partner_id'];
                $pl->debit = $account['debit'];
                $pl->credit = $account['credit'];
                $pl->date = (new \DateTime())->format('d-m-Y');
                $pl->save();

                $partner_liquidation_id = Yii::$app->db->getLastInsertID();
                $this->insertMovements($partner_liquidation_id, 'app\\\\modules\\\\accounting\\\\models\\\\AccountMovement', $search->searchMovements($pl->partner_distribution_model_has_partner_id));
                $this->insertMovements($partner_liquidation_id, 'app\\\\modules\\\\checkout\\\\models\\\\Payment', $search->searchPayments($pl->partner_distribution_model_has_partner_id));
                $this->insertMovements($partner_liquidation_id, 'app\\\\modules\\\\provider\\\\models\\\\ProviderPayment', $search->searchProviderPayments($pl->partner_distribution_model_has_partner_id));
            }

            $transaction->commit();
        } catch(\Exception $ex) {
            $transaction->rollBack();
            $this->messages[] = ['type'=>'error', 'message'=> $ex->getMessage()];
            error_log($ex->getMessage());
        }

        return true;
    }

    private function insertMovements($partner_liquidation_id, $class, $query)
    {
        $mainQuery = (new Query())
            ->select([new Expression($partner_liquidation_id), new Expression("'".$class."'" ), 'model_id', 'status'])
            ->from(['s'=> $query])
        ;
        
        Yii::$app->db->createCommand('INSERT INTO partner_liquidation_movement (partner_liquidation_id, class, model_id, type) ' .
            $mainQuery->createCommand()->rawSql
        )->execute();

    }

    /**
     * Crea
     * @param $account_movement_id
     * @param $partner_distribution_model_id
     */
    private function createPartnerLiquidation($partner_distribution_model_has_partner_id, $last_account_movement_id)
    {
        $pl = new PartnerLiquidation();
        $pl->partner_distribution_model_has_partner_id = $partner_distribution_model_has_partner_id;
        $pl->last_account_movement_id = $last_account_movement_id;
        $pl->date = (new \DateTime())->format('d-m-Y');
        $pl->save();
        return $pl;
    }

}