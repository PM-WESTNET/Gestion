<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 5/04/18
 * Time: 13:03
 */

namespace app\modules\westnet\reports\search;

use app\components\helpers\DbHelper;
use app\modules\accounting\models\Account;
use app\modules\config\models\Config;
use app\modules\westnet\models\NotifyPayment;
use app\modules\westnet\reports\models\ReportData;
use app\modules\westnet\reports\ReportsModule;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;
use yii\web\JsExpression;

class ReportSearch extends Model
{
    public $date_from;
    public $date_to;
    public $company_id;
    public $from;
    public $publicity_shape;

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
            [['date_from', 'date_to', 'company_id', 'from', 'publicity_shape'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'date_from' => ReportsModule::t('app', 'Date From'),
            'date_to' => ReportsModule::t('app', 'Date To'),
            'publicity_shape' => ReportsModule::t('app', 'Publicity Shape')
        ];
    }

    /**
     * Retorno la cantidad de contratos activos a una fecha determinada.
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
            ->select(['*'])
            ->from('contract c')
            ->andWhere(new Expression('c.status = \'active\' 
                    AND (( date_format(c.from_date, \'%Y%m%d\') <= :fecha ) && (c.to_date is null || date_format(c.to_date, \'%Y%m%d\') <= :fecha  )  )'))
            ->addParams([':fecha' => (int)$fecha->format('Ymd')]);
        return $query->count();
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

        $query = ReportData::find()
            ->where(['report' => ReportData::REPORT_ACTIVE_CONNECTION])
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
            ->select([new Expression('date_format(c.from_date, \'%Y-%m\') AS periodo'), new Expression('count(*) as alta'), new Expression('0 as baja')])
            ->from('contract c')
            ->where('c.status = \'active\'')
            ->groupBy(new Expression('date_format(c.from_date, \'%Y-%m\')'));

        $queryLow = new Query();
        $queryLow
            ->select([new Expression('date_format(c.to_date, \'%Y-%m\') AS periodo'), new Expression('0 as alta'), new Expression('count(*) as baja')])
            ->from('contract c')
            ->where('c.status = \'low\'')
            ->groupBy(new Expression('date_format(c.to_date, \'%Y-%m\')'));

        $queryActive->union($queryLow, true);

        $query = new Query();
        $query
            ->select(['periodo', new Expression('sum(alta) as alta'), new Expression('sum(baja) as baja'), new Expression('sum(alta) - sum(baja) as diferencia')])
            ->from(['t' => $queryActive])
            ->groupBy('periodo');

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
     * Busco el hitsotrico de contratos activos
     *
     * @param $params
     * @param $report
     * @return ReportData[]|array|\yii\db\ActiveRecord[]
     */
    public function findReportDataDebtBills($params, $report)
    {
        $this->load($params);

        $query = ReportData::find()
            ->where(['report' => $report])
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
     * Busco las bajas mensuales.
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
            new Expression('round((count(c.contract_id)*100)/ rd.value,2) as porcentage')
        ])
            ->from('contract c')
            ->leftJoin('report_data  rd ON rd.period = date_format(c.to_date, \'%Y%m\') and rd.report = \'active_connection\'')
            ->where(['c.status' => 'low'])
            ->groupBy(new Expression('date_format(c.to_date, \'%Y-%m\')'));

        if ($this->date_from) {
            $query->andWhere(['>=', 'date_format(c.to_date, \'%Y%m\')', (new \DateTime($this->date_from))->format('Ym')]);
        }

        if ($this->date_to) {
            $query->andWhere(['<=', 'date_format(c.to_date, \'%Y%m\')', (new \DateTime($this->date_to))->format('Ym')]);
        }

        if ($this->company_id) {
            $query->andWhere(['company_id' => $this->company_id]);
        }

        return $query->all();
    }


    /**
     * Busco las bajas mensuales.
     *
     * @param $params
     * @return array
     */
    public function findLowByReasonMonth($params)
    {
        $this->load($params);

        $query = new Query();
        $query->select([
            new Expression('date_format(c.to_date, \'%Y-%m\') AS period'),
            new Expression('count(c.contract_id) AS baja'), 'rd.value',
            new Expression('round((count(c.contract_id)*100)/ rd.value,2) as porcentage'),
            'cat.name', 'c.category_low_id'
        ])
            ->from('contract c')
            ->leftJoin('report_data  rd ON rd.period = date_format(c.to_date, \'%Y%m\') and rd.report = \'active_connection\'')
            ->leftJoin(DbHelper::getDbName(\Yii::$app->dbticket) . '.category cat', 'c.category_low_id = cat.category_id')
            ->where(['c.status' => 'low'])
            ->groupBy([new Expression('date_format(c.to_date, \'%Y-%m\')'), 'cat.name', 'c.category_low_id'])
            ->orderBy(['c.category_low_id' => SORT_ASC, new Expression('date_format(c.to_date, \'%Y-%m\') ASC')]);

        if ($this->date_from) {
            $query->andWhere(['>=', 'date_format(c.to_date, \'%Y%m\')', (new \DateTime($this->date_from))->format('Ym')]);
        }

        if ($this->date_to) {
            $query->andWhere(['<=', 'date_format(c.to_date, \'%Y%m\')', (new \DateTime($this->date_to))->format('Ym')]);
        }
        return $query->all();
    }

    /**
     * Diferencia entre pagos y cobros.
     *
     * @param $param
     * @return array
     */
    public function findCostEffectiveness($param)
    {
        $this->load($param);

        $queryPaymentCobrado = new Query();
        $queryPaymentCobrado
            ->select([new Expression("p.date as fecha"), new Expression('sum(pi.amount) as facturado'),new Expression('0 as pagos'),new Expression('0 AS pagos_employee'), new Expression('0 as pagos_account')])
            ->from(['payment p'])
            ->leftJoin('payment_item pi', 'p.payment_id = pi.payment_id')
            ->leftJoin('payment_method m', 'pi.payment_method_id = m.payment_method_id')
            ->where('m.payment_method_id is not null')
            ->groupBy(['p.date'])
        ;

        $queryPayment = new Query();
        $queryPayment
            ->select(['pp.date as fecha', new Expression('0 AS facturado'), 'pp.amount AS pagos', new Expression('0 AS pagos_employee'), new Expression('0 as pagos_account')])
            ->from(['provider_payment pp']);

        $queryEmployeePayment = new Query();
        $queryEmployeePayment
            ->select(['pp.date as fecha', new Expression('0 AS facturado'), new Expression('0 as pagos'), 'pp.amount AS pagos_employee', new Expression('0 as pagos_account')])
            ->from(['employee_payment pp']);


        $account = Account::findOne(['name'=>'GASTOS BANCARIOS']);

        $queryMovements = new Query();
        $queryMovements
            ->select(["am.date as fecha", new Expression('0 as facturado'),new Expression('0 as pagos'), new Expression('0 AS pagos_employee'), new Expression('coalesce(credit, debit) as pagos_account')])
            ->from(['account_movement am'])
            ->leftJoin('account_movement_item a', 'am.account_movement_id = a.account_movement_id')
            ->leftJoin('account_movement_relation a2', 'am.account_movement_id = a2.account_movement_id')
            ->leftJoin('account a3', 'a.account_id = a3.account_id')
            ->where('a2.account_movement_id is null and am.status = \'closed\' and a3.lft >= '.$account->lft.' and a3.rgt <='.$account->rgt)
        ;

        if ($this->date_from) {
            $queryPaymentCobrado->andWhere(['>=', 'p.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
            $queryPayment->andWhere(['>=', 'pp.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
            $queryEmployeePayment->andWhere(['>=', 'pp.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
            $queryMovements->andWhere(['>=', 'am.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
        }

        if ($this->date_to) {
            $queryPaymentCobrado->andWhere(['<=', 'p.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
            $queryPayment->andWhere(['<=', 'pp.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
            $queryEmployeePayment->andWhere(['<=', 'pp.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
            $queryMovements->andWhere(['<=', 'am.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
        }

        $queryPaymentCobrado->union($queryMovements, true);
        $queryPaymentCobrado->union($queryPayment, true);
        $queryPaymentCobrado->union($queryEmployeePayment, true);

        $query = new Query();
        $query
            ->select([
                new Expression('date_format(fecha, \'%Y-%m\') AS period'), new Expression('round(sum(facturado)) as facturado'),
                new Expression('round(sum(pagos)) as pagos'),
                new Expression('round(sum(pagos_employee)) as pagos_employee'),
                new Expression('round(sum(pagos_account)) as pagos_account'),
                new Expression('round(sum(facturado) - sum(pagos) - sum(pagos_employee) - sum(pagos_account)) as diferencia')
            ])
            ->from(['a' => $queryPaymentCobrado])
            ->groupBy([new Expression('date_format(fecha, \'%m/%Y\')'),])
            ->orderBy(['date_format(fecha, \'%Y%m\')' => SORT_ASC]);

        return $query->all();
    }

    /**
     * Recupera todos los pagos registrados agrupandolos por tipo de método de pago.
     *
     * @param $param
     * @return array
     */
    public function findPaymentsMethod($param)
    {
        $this->load($param);

        $queryPaymentCobrado = new Query();
        $queryPaymentCobrado
            ->select([new Expression("p.date as fecha"), new Expression('sum(pi.amount) as facturado'),
                new Expression('count(*) as pagos'), new Expression('m.name as payment_name') ])
            ->from(['payment p'])
            ->leftJoin('payment_item pi', 'p.payment_id = pi.payment_id')
            ->leftJoin('payment_method m', 'pi.payment_method_id = m.payment_method_id')
            ->where('m.payment_method_id is not null')
            ->groupBy(['pi.payment_method_id'])
        ;


        if ($this->date_from) {
            $queryPaymentCobrado->andWhere(['>=', 'p.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
        }

        if ($this->date_to) {
            $queryPaymentCobrado->andWhere(['<=', 'p.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
        }

        if($this->company_id) {
            $queryPaymentCobrado->andWhere(['p.company_id' => $this->company_id]);
        }

        return $queryPaymentCobrado->all();
    }

    /**
     * Busco las altas y bajas en el historico.
     *
     * @param $params
     * @return array
     */
    public function findUpsAndDowns($params)
    {
        return (new Query())
            ->select(['u.period', 'd.value as down', 'u.value as up'])
            ->from('report_data u')
            ->innerJoin('report_data d', 'u.period = d.period')
            ->where('( u.report = \'up\' and d.report = \'down\')')->all();
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
            ->groupBy(['m.payment_method_id', 'date_format(p.date, \'%Y-%m\')'])
        ;

        $queryProviderPayment = new Query();
        $queryProviderPayment
            ->select([new Expression("'Egreso Proveedores' as tipo"), 'm.name as descripcion', "date_format(pp.date, '%Y-%m') as fecha",
                new Expression('0 as cobrado'), new Expression('sum(i.amount) as pagado')])
            ->from(['provider_payment pp'])
            ->leftJoin('provider_payment_item i', 'pp.provider_payment_id = i.provider_payment_id')
            ->leftJoin('payment_method m', 'i.payment_method_id = m.payment_method_id')
            ->where('m.payment_method_id is not null')
            ->groupBy(['m.payment_method_id', 'date_format(pp.date, \'%Y-%m\')'])
        ;

        $queryEmployeePayment = new Query();
        $queryEmployeePayment
            ->select([new Expression("'Egreso Sueldos Empleados' as tipo"), 'm.name as descripcion', "date_format(pp.date, '%Y-%m') as fecha",
                new Expression('0 as cobrado'), new Expression('sum(i.amount) as pagado')])
            ->from(['employee_payment pp'])
            ->leftJoin('employee_payment_item i', 'pp.employee_payment_id = i.employee_payment_id')
            ->leftJoin('payment_method m', 'i.payment_method_id = m.payment_method_id')
            ->where('m.payment_method_id is not null')
            ->groupBy(['m.payment_method_id', 'date_format(pp.date, \'%Y-%m\')'])
        ;

        $account = Account::findOne(['name'=>'EGRESOS']);

        $queryMovements = new Query();
        $queryMovements
            ->select([new Expression("'Egreso' as tipo"), new Expression('CONCAT(a3.name, \' - \', am.description) AS descripcion'),
                "date_format(am.date, '%Y-%m') as fecha",
                new Expression('0 as cobrado'), new Expression('coalesce(credit, debit) as pagado')])
            ->from(['account_movement am'])
            ->leftJoin('account_movement_item a', 'am.account_movement_id = a.account_movement_id')
            ->leftJoin('account_movement_relation a2', 'am.account_movement_id = a2.account_movement_id')
            ->leftJoin('account a3', 'a.account_id = a3.account_id')
            ->where('a2.account_movement_id is null and am.status = \'closed\' and a3.lft >= '.$account->lft.' and a3.rgt <='.$account->rgt)
        ;

        if ($this->date_from) {
            $queryProviderPayment->andWhere(['>=', 'pp.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
            $queryEmployeePayment->andWhere(['>=', 'pp.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
            $queryPayment->andWhere(['>=', 'p.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
            $queryMovements->andWhere(['>=', 'am.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
        }

        if ($this->date_to) {
            $queryProviderPayment->andWhere(['<=', 'pp.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
            $queryEmployeePayment->andWhere(['<=', 'pp.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
            $queryPayment->andWhere(['<=', 'p.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
            $queryMovements->andWhere(['<=', 'am.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
        }

        $queryPayment->union($queryMovements, true);
        $queryPayment->union($queryProviderPayment, true);
        $queryPayment->union($queryEmployeePayment, true);


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

    /**
     * Devuelve la cantidad de informes de pago agrupados por periodo y medio de pago
     */
    public function notifyPaymentStatistics()
    {
        $query = (new Query())->select([new Expression("COUNT(*) as qty, pm.name as payment_method_name, date_format(FROM_UNIXTIME(np.created_at), '%Y%m') as period")])
            ->from('notify_payment np')
            ->leftJoin('payment_method pm', 'pm.payment_method_id = np.payment_method_id')
            ->where(['from' => NotifyPayment::FROM_APP])
            ->groupBy(['pm.name', "date_format(FROM_UNIXTIME(np.created_at), '%Y%m')"])
            ->orderBy(["date_format(FROM_UNIXTIME(np.created_at), '%Y%m')" => SORT_DESC])
            ->all();

        return $query;
    }

    /**
     * Devuelve la cantidad de extensiones de pago agrupadas por periodos (restandole los forzados de connexion que corresponden a los informes de pago).
     */
    public function paymentExtensionStatistics()
    {
        $payment_extension_product = Config::getValue('extend_payment_product_id');

        $query = (new Query())
            ->select([new Expression("COUNT(*) as payment_extension_qty, 0 as notify_payment_qty, date_format(date,'%Y%m') as period")])
            ->from('contract_detail')
            ->where(['product_id' => $payment_extension_product])
            ->groupBy([new Expression("date_format(date, '%Y%m')")]);

        $queryNotifyPayment = (new Query())
            ->select([new Expression("0 as payment_extension_qty, COUNT(*) as notify_payment_qty,date_format(FROM_UNIXTIME(np.created_at), '%Y%m') as period")])
            ->from('notify_payment np')
            ->groupBy(["date_format(FROM_UNIXTIME(np.created_at), '%Y%m')"])
            ->orderBy(["date_format(FROM_UNIXTIME(np.created_at), '%Y%m')" => SORT_DESC]);

        $mainQuery = $query->union($queryNotifyPayment, true);

        $query = new Query();
        $query
            ->select(['SUM(payment_extension_qty) as payment_extension_qty, SUM(notify_payment_qty) as notify_payment_qty', 'period'])
            ->from(['a' => $mainQuery])
            ->orderBy(['period' => SORT_DESC])
            ->groupBy(['period']);

        return $query->all();
    }

    public function searchCustomerByPublicityShape($params) {
        $this->load($params);

        $query = (new Query())
            ->select([
                new Expression('date_format(c.date_new, \'%Y-%m\') AS period'),
                new Expression('count(c.customer_id) as customer_qty'),
                'c.publicity_shape'
            ])->from('customer c');

        if($this->publicity_shape) {
            $query->andWhere(['c.publicity_shape' => $this->publicity_shape]);
        }

        if($this->date_from) {
            $query->andWhere(['>=', new Expression('date_format(c.date_new, \'%Y-%m\')'), (new \DateTime($this->date_from))->format('Y-m')]);
        }

        if($this->date_to) {
            $query->andWhere(['<=', new Expression('date_format(c.date_new, \'%Y-%m\')'), (new \DateTime($this->date_to))->format('Y-m')]);
        }

        $query->groupBy([new Expression('date_format(c.date_new, \'%Y-%m\')'), 'c.publicity_shape']);

        return $query->all();
    }

    /**
     * Devuelve la cantidad de informes de pago diferenciados por medio de pago
     */
    public function searchNotifyPayments($params)
    {
        $this->load($params);

        $query = (new Query())
            ->select([
                new Expression('COUNT(*) as qty'),
                new Expression("CONCAT(`from`, ' - ', payment_method.name) as name"),
                new Expression('payment_method.payment_method_id'),
                new Expression('`from`')
            ])->from('notify_payment')
        ->leftJoin('payment_method', 'payment_method.payment_method_id = notify_payment.payment_method_id')
        ->groupBy(['notify_payment.payment_method_id', 'from'])
        ;

        if($this->date_from) {
            $date_from = (new \DateTime($this->date_from))->format('Y-m-d');
            $query->andWhere(['>=', new Expression('date_format(notify_payment.date, \'%Y-%m-%d\')'), $date_from]);
        }

        if($this->date_to) {
            $date_to = (new \DateTime($this->date_to))->format('Y-m-d');
            $query->andWhere(['<=', new Expression('date_format(notify_payment.date, \'%Y-%m-%d\')'), $date_to]);
        }

        return $query->all();
    }

    /**
     * Devuelve la cantidad de informes de pago diferenciados por medio de pago
     */
    public function searchNotifyPaymentsByDate($params)
    {
        $this->load($params);

        $query = (new Query())
            ->select([
                new Expression('COUNT(*) as qty'),
                new Expression('date_format(date, \'%Y-%m-%d\') as date'),
                new Expression('`from`')
            ])->from('notify_payment')
            ->groupBy(['from', 'date'])
            ->orderBy('date')
        ;

        if($this->date_from) {
            $date_from = (new \DateTime($this->date_from))->format('Y-m-d');
            $query->andWhere(['>=', new Expression('date_format(notify_payment.date, \'%Y-%m-%d\')'), $date_from]);
        }

        if($this->date_to) {
            $date_to = (new \DateTime($this->date_to))->format('Y-m-d');
            $query->andWhere(['<=', new Expression('date_format(notify_payment.date, \'%Y-%m-%d\')'), $date_to]);
        }

        return $query->all();
    }

    /**
     * Devuelve la cantidad de extensiones de pago por períodos
     */
    public function searchPaymentExtensionQty($params) {

        $this->load($params);

        $query = (new Query())
            ->select([
                new Expression('COUNT(*) as qty'),
                new Expression('date_format(date, \'%Y-%m-%d\') as date'),
                new Expression('`from`')
            ])->from(['payment_extension_history'])
            ->groupBy(['date', 'from'])
            ->orderBy('date');

        if($this->date_from) {
            $date_from = (new \DateTime($this->date_from))->format('Y-m-d');
            $query->andWhere(['>=', 'date', $date_from]);
        }

        if($this->date_to) {
            $date_to = (new \DateTime($this->date_to))->format('Y-m-d');
            $query->andWhere(['<=','date', $date_to]);
        }

        return $query->all();
    }

    /**
     * Devuelve la cantidad de extensiones de pago por origen
     */
    public function searchPaymentExtensionQtyFrom($params) {

        $this->load($params);

        $query = (new Query())
            ->select([
                new Expression('COUNT(*) as qty'),
                new Expression('`from`')
            ])->from(['payment_extension_history'])
            ->groupBy(['date'])
            ->orderBy('date');

        if($this->date_from) {
            $date_from = (new \DateTime($this->date_from))->format('Y-m-d');
            $query->andWhere(['>=', 'date', $date_from]);
        }

        if($this->date_to) {
            $date_to = (new \DateTime($this->date_to))->format('Y-m-d');
            $query->andWhere(['<=','date', $date_to]);
        }

        $query->groupBy(['from']);

        return $query->all();
    }
}