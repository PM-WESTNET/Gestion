<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 12/05/16
 * Time: 12:07
 */

namespace app\modules\partner\models\search;

use app\modules\config\models\Config;
use app\modules\partner\models\Partner;
use app\modules\partner\models\PartnerDistributionModelHasPartner;
use app\modules\partner\models\PartnerLiquidation;
use Yii;
use yii\db\Expression;
use yii\db\Query;

class PartnerLiquidationSearch extends PartnerLiquidation
{
    public $partner_liquidation_id;
    public $type;
    public $description;
    public $fromDate;
    public $toDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['partner_id', 'partner_liquidation_id'], 'integer'],
            [['fromDate', 'toDate', 'type'], 'safe'],
            [['description', 'type'], 'string'],

        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'fromDate' => Yii::t('partner', 'fromDate'),
            'toDate' => Yii::t('partner', 'toDate'),
            'type' => Yii::t('partner', 'Type'),
            'description'=> Yii::t('partner', 'Description')

        ]);
    }

    public function searchAccounts($params)
    {
        $queryManual = (new Query())->select([
                new Expression("date_format(am.date, '%Y-%m-%d') AS fecha"),
                'am.partner_distribution_model_id',
                'pdm.company_id',
                'am.status',
                'sum(coalesce(ami.debit, 0)) AS debit',
                'sum(coalesce(ami.credit, 0)) AS credit'
            ])
            ->from('account_movement am')
            ->leftJoin('account_movement_item ami', 'am.account_movement_id = ami.account_movement_id')
            ->leftJoin('partner_distribution_model pdm', 'am.partner_distribution_model_id = pdm.partner_distribution_model_id')
            ->leftJoin('partner_liquidation_movement plm', "am.account_movement_id = plm.model_id AND plm.class = 'app\\\modules\\\accounting\\\models\\\AccountMovement'  and am.status = plm.type")
            ->leftJoin('company c', 'am.company_id = c.company_id')
            ->where( new Expression('ami.account_id IN (select account_id from partner)'))
            ->andWhere(['c.status'=> 'enabled'])
            ->andWhere('plm.partner_liquidation_movement_id IS NULL')
            ->groupBy([new Expression("date_format(am.date, '%Y-%m-%d')"), 'am.partner_distribution_model_id','company_id', 'am.status'])
        ;

        $queryPago = (new Query())->select([
            new Expression("date_format(pp.date, '%Y-%m-%d') AS fecha"),
            'pdm.partner_distribution_model_id',
            'pp.company_id',
            'pp.status',
            "round(sum(coalesce(if(pp.status='cancelled', -pp.amount, pp.amount), 0)), 2) AS amount"
        ])
            ->from('provider_payment pp')
            ->leftJoin('partner_distribution_model pdm', 'pp.partner_distribution_model_id = pdm.partner_distribution_model_id')
            ->leftJoin('partner_liquidation_movement plm', "pp.provider_payment_id = plm.model_id and plm.class = 'app\\\modules\\\provider\\\models\\\ProviderPayment' and pp.status = plm.type")
            ->leftJoin('company c', 'pp.company_id = c.company_id')
            ->andWhere('plm.partner_liquidation_movement_id IS NULL')
            ->andWhere(['c.status'=> 'enabled'])
            ->groupBy([new Expression("date_format(pp.date, '%Y-%m-%d')"), 'pdm.partner_distribution_model_id', 'pp.status'])
        ;

        $queryCobro = (new Query())->select([
            new Expression("date_format(p.date, '%Y-%m-%d') AS fecha"),
            'pdm.partner_distribution_model_id',
            'p.company_id',
            'p.status',
            "round(sum(coalesce(if(p.status='cancelled', -p.amount, p.amount), 0)), 2) AS amount"
        ])
            ->from('payment p')
            ->leftJoin('partner_distribution_model pdm', 'p.partner_distribution_model_id = pdm.partner_distribution_model_id')
            ->leftJoin('partner_liquidation_movement plm', "p.payment_id = plm.model_id and plm.class = 'app\\\modules\\\checkout\\\models\\\Payment' and p.status = plm.type")
            ->leftJoin('company c', 'p.company_id = c.company_id')
            ->andWhere('plm.partner_liquidation_movement_id IS NULL')
            ->andWhere(['c.status'=> 'enabled'])
            ->groupBy([new Expression("date_format(p.date, '%Y-%m-%d')"), 'pdm.partner_distribution_model_id', 'p.status'])
        ;

        $masterManual = (new Query())
            ->select([
                'pdmhp.partner_distribution_model_has_partner_id',
                'c.name AS company',
                'p.name AS partner',
                'pdmhp.percentage',
                'q.status',
                new Expression('sum(debit * pdmhp.percentage / 100)  AS debit'),
                new Expression('sum(credit * pdmhp.percentage / 100) AS credit')
            ])
            ->from(['q' => $queryManual])
            ->leftJoin('partner_distribution_model_has_partner pdmhp', 'q.partner_distribution_model_id = pdmhp.partner_distribution_model_id')
            ->leftJoin('company c', 'c.company_id = q.company_id')
            ->leftJoin('partner_distribution_model pdm', 'pdmhp.partner_distribution_model_id = pdm.partner_distribution_model_id')
            ->leftJoin('partner p', 'pdmhp.partner_id = p.partner_id')
            ->groupBy(['c.company_id', 'partner_distribution_model_has_partner_id', 'q.status'])
        ;

        $masterPago = clone $masterManual;
        $masterPago->select([
                'pdmhp.partner_distribution_model_has_partner_id',
                'c.name AS company',
                'p.name AS partner',
                'pdmhp.percentage',
                'q.status',
                new Expression('sum(amount * pdmhp.percentage / 100) AS debit'),
                new Expression('0 AS credit')
            ])
            ->from(['q' => $queryPago])
        ;

        $masterCobro = clone $masterManual;
        $masterCobro->select([
            'pdmhp.partner_distribution_model_has_partner_id',
            'c.name AS company',
            'p.name AS partner',
            'pdmhp.percentage',
            'q.status',
            new Expression('0 AS debit'),
            new Expression('sum(amount * pdmhp.percentage / 100) AS credit')
        ])->from(['q' => $queryCobro])
        ;

        $masterManual->union($masterCobro, true)->union($masterPago, true);

        $mainQuery = new Query();
        $mainQuery->select(['partner_distribution_model_has_partner_id','company', 'partner', 'percentage', 'sum(debit) as debit', 'sum(credit) as credit' ])
            ->from(['a'=>$masterManual])
            ->groupBy(['company','partner','percentage'])
            ->orderBy(['company'=>SORT_ASC, 'partner'=>SORT_DESC, ]);

        return $mainQuery;
    }

    public function searchMovements($partner_distribution_model_has_partner_id)
    {
        $inQuery = (new Query())
            ->select(['pdm.partner_distribution_model_id', 'am.account_movement_id as model_id', 'am.status'])
            ->from('account_movement am')
            ->leftJoin('account_movement_item ami', 'am.account_movement_id = ami.account_movement_id')
            ->leftJoin('partner_distribution_model pdm', 'am.partner_distribution_model_id = pdm.partner_distribution_model_id')
            ->leftJoin('partner_liquidation_movement plm', "am.account_movement_id = plm.model_id AND plm.class = 'app\\\\modules\\\\accounting\\\\models\\\\AccountMovement' AND am.status = plm.type")
            ->leftJoin('company c', 'am.company_id = c.company_id')
            ->where(new Expression('ami.account_id IN (select account_id from partner)'))
            ->andWhere('plm.partner_liquidation_movement_id IS NULL')
            ->andWhere(['c.status'=> 'enabled'])
        ;

        $query = (new Query())
            ->select(['pdmhp.partner_distribution_model_has_partner_id', 'q.model_id', 'q.status'])
            ->from(['q' => $inQuery])
            ->leftJoin('partner_distribution_model_has_partner pdmhp', 'q.partner_distribution_model_id = pdmhp.partner_distribution_model_id')
            ->where(['partner_distribution_model_has_partner_id'=>$partner_distribution_model_has_partner_id])
        ;
        return $query;
    }

    public function searchPayments($partner_distribution_model_has_partner_id)
    {
        $inQuery = (new Query())
            ->select(['pdm.partner_distribution_model_id', 'p.payment_id as model_id', 'p.status'])
            ->from('payment p')
            ->leftJoin('partner_distribution_model pdm', 'p.partner_distribution_model_id = pdm.partner_distribution_model_id')
            ->leftJoin('partner_liquidation_movement plm', "p.payment_id = plm.model_id AND plm.class = 'app\\\\modules\\\\checkout\\\\models\\\\Payment'  and p.status = plm.type")
            ->leftJoin('company c', 'p.company_id = c.company_id')
            ->andWhere('plm.partner_liquidation_movement_id IS NULL')
            ->andWhere(['c.status'=> 'enabled'])
        ;

        $query = (new Query())
            ->select(['pdmhp.partner_distribution_model_has_partner_id', 'q.model_id', 'q.status'])
            ->from(['q' => $inQuery])
            ->leftJoin('partner_distribution_model_has_partner pdmhp', 'q.partner_distribution_model_id = pdmhp.partner_distribution_model_id')
            ->where(['partner_distribution_model_has_partner_id'=>$partner_distribution_model_has_partner_id])
        ;
        return $query;
    }

    public function searchProviderPayments($partner_distribution_model_has_partner_id)
    {
        $inQuery = (new Query())
            ->select(['pdm.partner_distribution_model_id', 'pp.provider_payment_id as model_id', 'pp.status'])
            ->from('provider_payment pp')
            ->leftJoin('partner_distribution_model pdm', 'pp.partner_distribution_model_id = pdm.partner_distribution_model_id')
            ->leftJoin('partner_liquidation_movement plm', "pp.provider_payment_id = plm.model_id and plm.class = 'app\\\\modules\\\\provider\\\\models\\\\ProviderPayment'  and pp.status = plm.type")
            ->leftJoin('company c', 'pp.company_id = c.company_id')
            ->andWhere('plm.partner_liquidation_movement_id IS NULL')
            ->andWhere(['c.status'=> 'enabled'])
            ->distinct()
        ;

        $query = (new Query())
            ->select(['pdmhp.partner_distribution_model_has_partner_id', 'q.model_id', 'q.status'])
            ->from(['q' => $inQuery])
            ->leftJoin('partner_distribution_model_has_partner pdmhp', 'q.partner_distribution_model_id = pdmhp.partner_distribution_model_id')
            ->where(['partner_distribution_model_has_partner_id'=>$partner_distribution_model_has_partner_id])
        ;
        return $query;
    }

    public function searchPending($partner_distribution_model_id, $last_account_movement_id)
    {
        $payment_account_id = Config::getValue('partner_payment_account');
        $provider_payment_account_id = Config::getValue('partner_provider_payment_account');

        $query = new Query();
        $query->select(['c.company_id', 'am.account_movement_id', 'p.partner_id', 'pdm.partner_distribution_model_id',
                        'p.account_id as destination_account_id', 'ami.account_id as origin_account_id', 'pdmhp.partner_distribution_model_has_partner_id'])
            ->from('company as c')
            ->innerJoin('partner_distribution_model pdm', 'c.company_id = pdm.company_id')
            ->leftJoin('partner_distribution_model_has_partner pdmhp', 'pdm.partner_distribution_model_id = pdmhp.partner_distribution_model_id')
            ->leftJoin('partner p', 'pdmhp.partner_id = p.partner_id')
            ->leftJoin('account_movement am', 'pdm.partner_distribution_model_id = am.partner_distribution_model_id')
            ->leftJoin('account_movement_item ami', 'ami.account_movement_id = am.account_movement_id')
            ->leftJoin('account a', 'ami.account_id = a.account_id')
            ->leftJoin('account av', 'a.lft >= av.lft AND a.rgt <= av.rgt')
            ->groupBy(['pdm.partner_distribution_model_id', 'account_movement_id', 'p.partner_id', 'p.account_id', 'ami.account_id', 'pdmhp.partner_distribution_model_has_partner_id']);


        $queryDebit  = clone $query;
        $queryCredit = clone $query;

        // seteo la consulta de debito
        $queryDebit->addSelect([new Expression("0 as type"), '(sum(ami.debit) * (pdmhp.percentage / 100)) AS amount'])
            ->where(['av.account_id' =>$payment_account_id])
            ->andWhere(['>', 'am.account_movement_id', $last_account_movement_id])
            ->andWhere(['=', 'pdm.partner_distribution_model_id', $partner_distribution_model_id])
        ;

        // seteo la consulta de credito
        $queryCredit->addSelect([new Expression("1 as type"),'(sum(ami.credit) * (pdmhp.percentage / 100)) AS amount'])
            ->where(['av.account_id' =>$provider_payment_account_id])
            ->andWhere(['>', 'am.account_movement_id', $last_account_movement_id])
            ->andWhere(['=', 'pdm.partner_distribution_model_id', $partner_distribution_model_id])
        ;

        $queryDebit->union($queryCredit);

        $mainQuery = new Query();
        $mainQuery->select(['type','partner_distribution_model_id', 'company_id', 'account_movement_id', 'partner_id',
            'destination_account_id', 'origin_account_id', 'partner_distribution_model_has_partner_id',  'sum(amount) as amount' ])
            ->from(['a'=>$queryDebit])
            ->groupBy(['type','partner_distribution_model_id', 'partner_id', 'destination_account_id', 'origin_account_id', 'partner_distribution_model_has_partner_id'])
            ->having('sum(amount)>0')
        ;

        return $mainQuery;
    }

    public function searchLiquidations($params)
    {
        $query = (new Query())
            ->select(['pl.partner_liquidation_id', 'pdm.name', 'pl.date',  'pl.debit', 'pl.credit'])
            ->from('partner_liquidation pl')
            ->leftJoin('partner_distribution_model_has_partner pdmhp', 'pl.partner_distribution_model_has_partner_id = pdmhp.partner_distribution_model_has_partner_id')
            ->leftJoin('partner_distribution_model pdm', 'pdmhp.partner_distribution_model_id = pdm.partner_distribution_model_id')
        ;
        return $query;
    }

    public function searchLiquidationItems($params)
    {

        $this->load($params);

        $query = (new Query())
            ->select([
                new Expression("if(plm.class = 'app\\\\modules\\\\accounting\\\\models\\\\AccountMovement', 'Movimiento', if(plm.class = 'app\\\\modules\\\\checkout\\\\models\\\\Payment', 'Cobro', 'Pago' )) as type"),
                new Expression("coalesce(am.date, p.date, pp.date) as date"),
                'plm.model_id', new Expression("coalesce(am.description, p.concept, pp.description) as description"),
                new Expression("coalesce((select sum(ami.debit) from account_movement_item ami where ami.account_movement_id = am.account_movement_id), p.amount, pp.amount) as amount")
            ])
            ->from("partner_liquidation_movement plm")
            ->leftJoin("account_movement am", "plm.model_id = am.account_movement_id and plm.class = 'app\\\\modules\\\\accounting\\\\models\\\\AccountMovement'")
            ->leftJoin("payment p", "plm.model_id = p.payment_id and plm.class = 'app\\\\modules\\\\checkout\\\\models\\\\Payment'")
            ->leftJoin("provider_payment pp", "plm.model_id = pp.provider_payment_id and plm.class = 'app\\\\modules\\\\provider\\\\models\\\\ProviderPayment'")
        ;

        $query->where(['plm.partner_liquidation_id'=>$this->partner_liquidation_id]);

        if(!empty($this->type)) {
            $query->andWhere(["if(plm.class = 'app\\modules\\accounting\\models\\AccountMovement', 'Movimiento', if(plm.class = 'app\\modules\\checkout\\models\\Payment', 'Cobro', 'Pago' ))" => $this->type])
            ;
        }

        $query->andFilterWhere([ "like", "coalesce(am.description, p.concept, pp.description)",$this->description ]) ;

        if(!empty($this->fromDate)) {
            $date = new \DateTime($this->fromDate);
            $query->andWhere([">=", "coalesce(am.date, p.date, pp.date)",$date->format('Y-m-d')])
            ;
        }

        if(!empty($this->toDate)) {
            $date = new \DateTime($this->toDate);
            $query->andWhere(["<=", "coalesce(am.date, p.date, pp.date)",$date->format('Y-m-d')])
            ;
        }

        return $query;
    }
}
