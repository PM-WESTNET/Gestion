<?php

namespace app\modules\westnet\reports\search;

use app\modules\accounting\models\Account;
use app\modules\config\models\Config;
use app\modules\westnet\reports\models\ReportData;
use app\modules\westnet\reports\ReportsModule;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;
use app\modules\westnet\reports\models\ReportCompanyData;

class ReportCompanySearch extends Model
{
    public $date_from;
    public $date_to;

    public $company_id;

    public function init()
    {
        parent::init();
        $this->date_from = (new \DateTime('first day of last year'))->format('01-01-Y');
        $this->date_to = (new \DateTime('last day of this month'))->format('d-m-Y');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_from', 'date_to'], 'string'],
            [['date_from', 'date_to', 'company_id'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'date_from' => ReportsModule::t('app', 'Date From'),
            'date_to' => ReportsModule::t('app', 'Date To'),
            'company_id' => ReportsModule::t('app', 'Company'),
        ];
    }

    /**
     * Retorno un array con la cantidad de contratos activos a una fecha determinada agrupados por empresa.
     * [company_id] =>  contract_qty
     * @param $fecha \DateTime
     * @return int
     */
    public function countActiveContracts($fecha = null)
    {
        if (!$fecha) {
            $fecha = new \DateTime('now');
        }

        $query = new Query();
        $query
            ->select(['c.*', 'cus.company_id'])
            ->from('contract c')
            ->leftJoin('customer cus', 'cus.customer_id = c.customer_id')
            ->andWhere(new Expression('c.status = \'active\'
                    AND (( date_format(c.from_date, \'%Y%m%d\') <= :fecha ) && (c.to_date is null || date_format(c.to_date, \'%Y%m%d\') <= :fecha  )  )'))
            ->addParams([':fecha' => (int)$fecha->format('Ymd')]);

        $total_count = [];
        foreach ($query->all() as $contract_qty) {
            $total_count[$contract_qty['company_id']] = (array_key_exists($contract_qty['company_id'], $total_count) ?  $total_count[$contract_qty['company_id']] : 0 ) + 1;
        }

        return $total_count;
    }

    /**
     * Busco el hitsotrico de contratos activos
     *
     * @param $params
     * @return ReportData[]|array|\yii\db\ActiveRecord[]
     */
    public function findReportDataActiveContracts($params)
    {
        $this->load($params);

        $query = ReportCompanyData::find()
            ->where(['report' => ReportData::REPORT_ACTIVE_CONNECTION])
            ->orderBy(['period' => SORT_ASC]);

        if ($this->date_from) {
            $query->andWhere(['>=', 'period', (new \DateTime($this->date_from))->format('Ym')]);
        }

        if ($this->date_to) {
            $query->andWhere(['<=', 'period', (new \DateTime($this->date_to))->format('Ym')]);
        }

        if($this->company_id) {
            $query->andWhere(['company_id' => $this->company_id]);
        }

        return $query->all();
    }

    /**
     * Se buscan la variacion de clientes por mes
     *
     * @param $params
     * @return array
     */
    public function findCustomerVariationPerMonth($params)
    {
        $this->load($params);

        $queryActive = new Query();
        $queryActive
            ->select([new Expression('date_format(c.from_date, \'%Y-%m\') AS periodo'), new Expression('count(*) as alta'), new Expression('0 as baja'), 'cus.company_id'])
            ->leftJoin('customer cus', 'cus.customer_id = c.customer_id')
            ->from('contract c')
            ->where('c.status = \'active\'')
            ->groupBy(new Expression('date_format(c.from_date, \'%Y-%m\'), cus.company_id'));

        $queryLow = new Query();
        $queryLow
            ->select([new Expression('date_format(c.to_date, \'%Y-%m\') AS periodo'), new Expression('0 as alta'), new Expression('count(*) as baja'), 'cus.company_id'])
            ->leftJoin('customer cus', 'cus.customer_id = c.customer_id')
            ->from('contract c')
            ->where('c.status = \'low\'')
            ->groupBy(new Expression('date_format(c.to_date, \'%Y-%m\'), cus.company_id'));


        $queryActive->union($queryLow, true);

        $query = new Query();
        $query
            ->select(['periodo', new Expression('sum(alta) as alta'), new Expression('sum(baja) as baja'), new Expression('sum(alta) - sum(baja) as diferencia'), 'company_id'])
            ->from(['t' => $queryActive])
            ->groupBy(['periodo', 'company_id']);

        return $query->all();
    }

    /**
     * Busco el hitsotrico de contratos activos
     *
     * @param $params
     * @return ReportData[]|array|\yii\db\ActiveRecord[]
     */
    public function findReportDataCompanyPassive($params)
    {
        $this->load($params);

        $query = ReportData::find()
            ->where(['report' => ReportData::REPORT_COMPANY_PASSIVE])
            ->orderBy(['period' => SORT_ASC]);

        if ($this->date_from) {
            $query->andWhere(['>=', 'period', (new \DateTime($this->date_from))->format('Ym')]);
        }

        if ($this->date_to) {
            $query->andWhere(['<=', 'period', (new \DateTime($this->date_to))->format('Ym')]);
        }

        return $query->all();
    }

    /**
     * Busco el historico de contratos activos
     *
     * @param $params
     * @param $report
     * @return ReportData[]|array|\yii\db\ActiveRecord[]
     */
    public function findReportDataDebtBills($params, $report)
    {
        $this->load($params);

        $query = ReportCompanyData::find()
            ->where(['report' => $report])
            ->orderBy(['period' => SORT_ASC]);

        if ($this->date_from) {
            $query->andWhere(['>=', 'period', (new \DateTime($this->date_from))->format('Ym')]);
        }

        if ($this->date_to) {
            $query->andWhere(['<=', 'period', (new \DateTime($this->date_to))->format('Ym')]);
        }

        if ($this->company_id) {
            $query->andWhere(['company_id' => $this->company_id]);
        }

        return $query->all();
    }

    /**
     * Busco las bajas mensuales por empresa.
     *
     * @param $params
     */
    public function findLowByMonth($params)
    {
        $this->load($params);

        $query = new Query();
        $query->select([
            new Expression('date_format(c.to_date, \'%Y-%m\') AS period'),
            new Expression('count(c.contract_id) AS baja'), 'rd.value',
            new Expression('round((count(c.contract_id)*100)/ rd.value,2) as porcentage'),
            'cus.company_id'
        ])
            ->from('contract c')
            ->leftJoin('customer cus', 'cus.customer_id = c.customer_id')
            ->leftJoin('report_company_data  rd ON rd.period = date_format(c.to_date, \'%Y%m\') and rd.report = \'active_connection\' and rd.company_id = cus.company_id')
            ->where(['c.status' => 'low'])
            ->groupBy(new Expression('date_format(c.to_date, \'%Y-%m\'), cus.company_id'));

        if ($this->date_from) {
            $query->andWhere(['>=', 'date_format(c.to_date, \'%Y%m\')', (new \DateTime($this->date_from))->format('Ym')]);
        }

        if ($this->date_to) {
            $query->andWhere(['<=', 'date_format(c.to_date, \'%Y%m\')', (new \DateTime($this->date_to))->format('Ym')]);
        }

        if($this->company_id) {
            $query->andWhere(['cus.company_id' => $this->company_id]);
        }

        return $query->all();
    }

    /**
     * Diferencia entre pagos y cobros por empresa.
     *
     * @param $param
     * @return array
     */
    public function findCostEffectiveness($param)
    {
        $this->load($param);

        $queryPaymentCobrado = new Query();
        $queryPaymentCobrado
            ->select([new Expression("p.date as fecha"), new Expression('sum(pi.amount) as facturado'),new Expression('0 as pagos'), new Expression('0 as pagos_account'), 'p.company_id'])
            ->from(['payment p'])
            ->leftJoin('payment_item pi', 'p.payment_id = pi.payment_id')
            ->leftJoin('payment_method m', 'pi.payment_method_id = m.payment_method_id')
            ->where('m.payment_method_id is not null')
            ->groupBy(['p.date'])
        ;

        $queryPayment = new Query();
        $queryPayment
            ->select(['pp.date as fecha', new Expression('0 AS facturado'), 'pp.amount AS pagos', new Expression('0 as pagos_account'), 'pp.company_id'])
            ->from(['provider_payment pp']);

        if ($this->date_from) {
            $queryPaymentCobrado->andWhere(['>=', 'p.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
            $queryPayment->andWhere(['>=', 'pp.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
        }

        if ($this->date_to) {
            $queryPaymentCobrado->andWhere(['<=', 'p.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
            $queryPayment->andWhere(['<=', 'pp.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
        }

        if($this->company_id) {
            $queryPaymentCobrado->andWhere(['p.company_id' => $this->company_id]);
            $queryPayment->andWhere(['pp.company_id' => $this->company_id]);
        }

        $queryPaymentCobrado->union($queryPayment, true);
        $query = new Query();
        $query
            ->select([
                new Expression('date_format(fecha, \'%Y-%m\') AS period'), new Expression('round(sum(facturado)) as facturado'),
                new Expression('round(sum(pagos)) as pagos'),
                new Expression('round(sum(pagos_account)) as pagos_account'),
                new Expression('round(sum(facturado) - sum(pagos) - sum(pagos_account)) as diferencia'),
                'company_id'
            ])
            ->from(['a' => $queryPaymentCobrado])
            ->groupBy([new Expression('date_format(fecha, \'%m/%Y\'), company_id')])
            ->orderBy(['date_format(fecha, \'%Y%m\')' => SORT_ASC]);

        return $query->all();
    }

    /**
     * Busco las altas y bajas en el historico.
     *
     * @param $params
     * @return array
     */
    public function findUpsAndDowns($params)
    {
        $this->load($params);

        $query = (new Query())
            ->select(['u.period', 'd.value as down', 'u.value as up', 'u.company_id'])
            ->from('report_company_data u')
            ->innerJoin('report_company_data d', 'u.period = d.period and u.company_id = d.company_id')
            ->where('( u.report = \'up\' and d.report = \'down\')');

        if($this->company_id) {
            $query->andWhere(['u.company_id' => $this->company_id]);
        }

        return $query->all();
    }

    public function findRetenciones($params)
    {
        $this->load($params);

        $account = Account::findOne(['name'=>'RETENCIONES']);

        $query = (new Query())
            ->select([new Expression("'Retenciones' as tipo"), 'a.name as descripcion', "date_format(am.date, '%Y-%m') as fecha",
                new Expression('0 as cobrado'), new Expression('sum(ami.debit) as pagado')])
            ->from('account a')
            ->leftJoin('account_movement_item ami', 'ami.account_id = a.account_id')
            ->leftJoin('account_movement am', 'am.account_movement_id = ami.account_movement_id')
            ->where('a.lft >= '.$account->lft.' and a.rgt <='.$account->rgt)
            ->groupBy('a.account_id', 'am.date')
        ;


        if ($this->date_from) {
            $query->andWhere(['>=', 'am.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
        }

        if ($this->date_to) {
            $query->andWhere(['<=', 'am.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
        }

        return $query;
    }

    /**
     * Consulta de Ingresos y egresos.
     * @param $params
     * @return Query
     */
    public function findInOut($params)
    {
        $this->load($params);

        $queryPayment = new Query();
        $queryPayment
            ->select([new Expression("'Ingreso' as tipo"), 'm.name as descripcion', "date_format(p.date, '%Y-%m') as fecha", new Expression('sum(pi.amount) as cobrado'),
                new Expression('0 as pagado')])
            ->from(['payment p'])
            ->leftJoin('payment_item pi', 'p.payment_id = pi.payment_id')
            ->leftJoin('payment_method m', 'pi.payment_method_id = m.payment_method_id')
            ->where('m.payment_method_id is not null')
            ->groupBy(['m.payment_method_id', 'date_format(p.date, \'%Y-%m\'), p.company_id'])
        ;

        $queryProviderPayment = new Query();
        $queryProviderPayment
            ->select([new Expression("'Egreso Proveedores' as tipo"), 'm.name as descripcion', "date_format(pp.date, '%Y-%m') as fecha",
                new Expression('0 as cobrado'), new Expression('sum(i.amount) as pagado')])
            ->from(['provider_payment pp'])
            ->leftJoin('provider_payment_item i', 'pp.provider_payment_id = i.provider_payment_id')
            ->leftJoin('payment_method m', 'i.payment_method_id = m.payment_method_id')
            ->where('m.payment_method_id is not null')
            ->groupBy(['m.payment_method_id', 'date_format(pp.date, \'%Y-%m\'), pp.company_id'])
        ;

        if ($this->date_from) {
            $queryProviderPayment->andWhere(['>=', 'pp.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
            $queryPayment->andWhere(['>=', 'p.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
        }

        if ($this->date_to) {
            $queryProviderPayment->andWhere(['<=', 'pp.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
            $queryPayment->andWhere(['<=', 'p.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
        }

        if($this->company_id) {
            $queryProviderPayment->andWhere(['pp.company_id' => $this->company_id]);
            $queryPayment->andWhere(['p.company_id' => $this->company_id]);
        }

        $queryPayment->union($queryProviderPayment, true);

        if(Config::getValue('add_retenciones_into_in_out_report')){
            $queryRetenciones = $this->findRetenciones($params);
            $queryPayment->union($queryRetenciones, true);
        }

        $query = new Query();
        $query
            ->select([ 'tipo', 'descripcion', 'fecha as periodo', 'round(cobrado) as cobrado', 'round(pagado) as pagado'])
            ->from(['a' => $queryPayment])
            ->orderBy([ 'fecha' => SORT_DESC, 'tipo'=> SORT_DESC])
        ;

        return $query;
    }

    /**
     * Totales de FindInOut
     * @param $params
     * @return array|bool
     */
    public function findInOutTotals($params)
    {
        $queryInOut = $this->findInOut($params);
        $query  = (new Query());
        $query->select(['sum(p.cobrado) as cobrado', 'sum(p.pagado) as pagado'])
            ->from([ 'p' => $queryInOut]);

        return $query->one();
    }

    /**
     * @param $params
     * @return Query
     */
    public function findMovements($params)
    {
        $this->load($params);
        $query = new Query();
        $query
            ->select(['am.account_movement_id', new Expression("'Ingreso/Egreso' as tipo"),
                    "CONCAT(group_concat(a3.name, ' '), ' - ', am.description) AS descripcion",
                    'date_format(am.date, \'%Y-%m\') as fecha', 'coalesce(credit, debit) as monto'])
            ->from(['account_movement am'])
            ->leftJoin('account_movement_item a', 'am.account_movement_id = a.account_movement_id')
            ->leftJoin('account_movement_relation a2', 'am.account_movement_id = a2.account_movement_id')
            ->leftJoin('account a3', 'a.account_id = a3.account_id')
            ->where('a2.account_movement_id is null and am.status = \'closed\'')
            ->groupBy(['am.account_movement_id'])
        ;
        if ($this->date_from) {
            $query->andWhere(['>=', 'am.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
        }

        if ($this->date_to) {
            $query->andWhere(['<=', 'am.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
        }

        return $query;
    }

    /**
     * Totales de FindInOut
     * @param $params
     * @return array|bool
     */
    public function findMovementsTotals($params)
    {
        $queryInOut = $this->findMovements($params);
        $query  = (new Query());
        $query->select(['sum(p.monto)'])
            ->from([ 'p' => $queryInOut]);

        return $query->one();
    }


}