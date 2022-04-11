<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 5/04/18
 * Time: 13:03
 */

namespace app\modules\westnet\reports\search;

use Yii;
use yii\db\Query;
use yii\base\Model;
use yii\db\Expression;
use yii\web\JsExpression;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use app\components\helpers\DbHelper;
use app\modules\config\models\Config;
use app\modules\accounting\models\Account;
use yii\debug\models\timeline\DataProvider;
use app\modules\westnet\models\NotifyPayment;
use app\modules\westnet\reports\ReportsModule;
use app\modules\westnet\reports\models\ReportData;

class ReportSearch extends Model
{
    public $date_from;
    public $date_to;
    public $company_id;
    public $from;
    public $publicity_shape;

    public $max;

    public $code;
    public $name;
    public $lastname;
    public $name_product;
    public $fullname;
    public $node;
    public $speed;
    public $date;
    public $date2;

    public function init($date_from = null, $date_to = null)
    {
        parent::init();
        $this->date_from = $date_from;
        $this->date_to = $date_to;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_from', 'date_to','name','lastname','name_product','fullname','node', 'speed'], 'string'],
            [['date_from', 'date_to', 'company_id', 'from', 'publicity_shape','date','date2'], 'safe'],
            [['code'],'number']
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

    /**
     * Busca las notificaciones con los parámetros indicados
     * @param $params
     * @return ReportData[]|array|\yii\db\ActiveRecord[]
     */
    public function findPushNotifications($params)
    {
        $this->load($params);

        $query = (new Query())
            ->select(['n.notification_id as notification_id',
                'n.name as notification_name',
                'mp.send_timestamp',
                (new Expression("COUNT(CASE WHEN `mphua`.sent_at IS NOT NULL THEN 1 END) as count_sent")),
                (new Expression("COUNT(CASE WHEN `mphua`.notification_read = 1 THEN 1 END) as count_read")),
                'mphua.mobile_push_id'
            ])
            ->from(DbHelper::getDbName(\Yii::$app->db) .'.mobile_push_has_user_app mphua' )
            ->leftJoin(DbHelper::getDbName(\Yii::$app->db).'.mobile_push mp', 'mp.mobile_push_id= mphua.mobile_push_id')
            ->leftJoin(DbHelper::getDbName(\Yii::$app->dbnotifications) .'.notification n', 'mp.notification_id = n.notification_id')
            ->groupBy('mp.mobile_push_id')
            ->orderBy(['mp.created_at' => SORT_DESC])
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        return $dataProvider;
    }

    public function firstdataDebitByDate($params) 
    {
        $this->load($params);

        $query= (new Query())
            ->select(["date_format(from_unixtime(created_at), '%m-%Y') as period", "COUNT(customer_id) as altas"])
            ->from('firstdata_automatic_debit');

        if (!empty($this->date_from)) {
            $query->andWhere(['>=', 'created_at', strtotime(Yii::$app->formatter->asDate($this->date_from, 'yyyy-MM-dd'))]);
        }

        if (!empty($this->date_to)) {
            $query->andWhere(['<', 'created_at', (strtotime(Yii::$app->formatter->asDate($this->date_to, 'yyyy-MM-dd')) + 86400)]);
        }

        $query->groupBy([new Expression('date_format(from_unixtime(created_at), \'%m-%Y\')')]);
        //$query->groupBy(['period']);

        $points = [];
        $data = [];
        $labels = $this->getDateLabels($this->date_from, $this->date_to);
        $max = 0;

        foreach($labels as $label) {
            $points[$label] = [
                'x' => $label
            ];
        }

        foreach($query->all() as $item) {
            $points[$item['period']] = $item['altas'];
            if ($item['altas'] > $max) {
                $max = $item['altas'];
            }
        }

        $this->max = $max;

        foreach($points as $point) {
            $data[] = $point;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => Yii::t('app', 'Firstdata Automatic Debits'),
                    'backgroundColor' => "rgb(255,255,255,0)",
                    'borderColor' => "#2da7f5",
                    'data' => $data
                ]
            ]
        ];
    }

    private function getDateLabels($from_date = null, $to_date = null) {
        
        $labels = [];

        if (!empty($from_date)) {
            $from = (new \DateTime())->setTimestamp(strtotime(Yii::$app->formatter->asDate($from_date, 'yyyy-MM-dd')))->modify('first day of this month')->getTimestamp();
        }else {
            $from = (new \DateTime())->modify('-1 year')->getTimestamp();
        }

        if (!empty($to_date)) {
            $to = (new \DateTime())->setTimestamp(strtotime(Yii::$app->formatter->asDate($to_date, 'yyyy-MM-dd')))->modify('last day of this month')->getTimestamp();
        }else {
            $to = (new \DateTime())->modify('last day of this month')->getTimestamp();
        }

        for($i = $from; $i <= $to; $i = $i + (86400 * 31)) {
            $labels[] = Yii::$app->formatter->asDate($i, 'MM-yyyy');
        }

        if (array_search(Yii::$app->formatter->asDate($to, 'MM-yyyy'), $labels) < 0) {
            $labels[] = Yii::$app->formatter->asDate($to, 'MM-yyyy');
        }


        return $labels;
    }

    /**
     * Se busca a clientes con sus respectivas velocidades
     *
     * @param $params
     * @return array
     */
    public function findCustomerBySpeed($params)
    {
        $this->load($params);

        $query = 'SELECT cu.customer_id, cu.name, cu.lastname, cu.code, co.contract_id, cd.contract_detail_id, pr.product_id, pr.name as name_product FROM customer cu
                LEFT JOIN contract co ON co.customer_id = cu.customer_id 
                LEFT JOIN contract_detail cd ON cd.contract_id = co.contract_id
                LEFT JOIN product pr ON pr.product_id = cd.product_id
                WHERE pr.status = "enabled" AND pr.type = "plan" ORDER BY pr.name ASC';

        if(!empty($this->code) || !empty($this->name) || !empty($this->lastname) || !empty($this->name_product)){

            if(!empty($this->code)){
                $query_new = str_replace('ORDER BY','AND cu.code LIKE :code ORDER BY',$query);
                $result = Yii::$app->db->createCommand($query_new)->bindValue('code','%'.$this->code.'%')->queryAll();
                
            }

            if (!empty($this->name)) {
                $query_new = str_replace('ORDER BY','AND cu.name LIKE :name ORDER BY',$query);
                $result = Yii::$app->db->createCommand($query_new)->bindValue('name','%'.$this->name.'%')->queryAll();


            }
            if (!empty($this->lastname)) {
                $query_new = str_replace('ORDER BY','AND cu.lastname LIKE :lastname ORDER BY',$query);
                $result = Yii::$app->db->createCommand($query_new)->bindValue('lastname','%'.$this->lastname.'%')->queryAll();


            }
            if (!empty($this->name_product)) {
                $query_new = str_replace('ORDER BY','AND pr.name LIKE :name_product ORDER BY',$query);
                $result = Yii::$app->db->createCommand($query_new)->bindValue('name_product','%'.$this->name_product.'%')->queryAll();
            }
        }else{
            $result = Yii::$app->db->createCommand($query)->queryAll();
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $result,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);
            
        return $dataProvider;
    }

    /**
     * Se busca a clientes con sus respectivos planes
     *
     * @param $params
     * @return array
     */
    public function findCustomerByPlan($params)
    {
        $this->load($params);

        $query = 'SELECT cu.code, CONCAT_WS(" ",cu.name, cu.lastname) as fullname, pr.name as name_product, no.name as node, co.date FROM customer cu
                LEFT JOIN contract co ON co.customer_id = cu.customer_id 
                LEFT JOIN contract_detail cd ON cd.contract_id = co.contract_id
                LEFT JOIN product pr ON pr.product_id = cd.product_id
                LEFT JOIN connection con ON con.contract_id = co.contract_id
                LEFT JOIN node no ON no.node_id = con.node_id
                WHERE co.status = "active" AND pr.status = "enabled" AND pr.type = "plan" ORDER BY cu.code ASC';
        $bind_values = [];
        if(!empty($this->code)  || !empty($this->fullname) || !empty($this->name_product) || !empty($this->speed) || !empty($this->node) || !empty($this->date)){

            if(!empty($this->code)){
                $query = str_replace('ORDER BY','AND cu.code LIKE :code ORDER BY',$query);
                $bind_values[] = ['code','%'.$this->code.'%'];
                
            }

            if (!empty($this->fullname)) {
                $query = str_replace('ORDER BY','AND CONCAT_WS(" ",cu.name, cu.lastname) LIKE :fullname ORDER BY',$query);
                $bind_values[] = ['fullname','%'.$this->fullname.'%'];

            }
            if (!empty($this->name_product)) {
                $name_product = '';
                if(strpos("fibra", strtolower($this->name_product)) !== false){
                    $name_product = "FTTH";
                }else if(strpos("wireless", strtolower($this->name_product)) !== false){
                    $name_product = "WiFi";
                }
        
                $query = str_replace('ORDER BY','AND pr.name LIKE :name_product ORDER BY',$query);
                $bind_values[] = ['name_product','%'.$name_product.'%'];


            }
            if (!empty($this->speed)) {
                $query = str_replace('ORDER BY','AND pr.name LIKE :name_product ORDER BY',$query);
                $bind_values[] = ['name_product','%'.$this->speed.'%'];


            }
            if(!empty($this->node)){
                $query = str_replace('ORDER BY','AND no.name LIKE :node ORDER BY',$query);
                $bind_values[] = ['node','%'.$this->node.'%'];
                
            }
            if(!empty($this->date) && empty($this->date2)){
                $query = str_replace('ORDER BY','AND co.date >= :date ORDER BY',$query);
                $bind_values[] = ['date',$this->date];
            }
            if(!empty($this->date) && !empty($this->date2)){
                $query = str_replace('ORDER BY','AND co.date >= :date AND co.date <= :date2 ORDER BY',$query);
                $bind_values[] = ['date',$this->date];
                $bind_values[] = ['date2',$this->date2];
            }

        }

        $result = Yii::$app->db->createCommand($query);

        if(!empty($bind_values)){
            foreach ($bind_values as $key => $value) {
                $result->bindValue($value[0],$value[1]);
            }
            
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $result->queryAll(),
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);
            
        return $dataProvider;
    }

    /**
     * Se busca a clientes con sus respectivos planes
     *
     * @param $params
     * @return array
     */
    public function findNumberOfClientsForConnection($params)
    {
        $this->load($params);
        if(!empty($params['date']) && !empty($params['date2'])){
            $query = 'SELECT count(cu.customer_id) as count_clients FROM customer cu
                  LEFT JOIN contract co ON co.customer_id = cu.customer_id
                  LEFT JOIN connection con ON con.contract_id = co.contract_id
                  WHERE cu.status = "enabled" AND co.status = "active" AND con.ip4_1 IS NOT NULL AND co.date >= :date AND co.date <= :date2';

            return Yii::$app->db->createCommand($query)->bindValue('date',$params['date'])->bindValue('date2',$params['date2'])->queryOne();
        }else{
            return ["count_clients" => "error"];
        }
        
    }

    public function findCustomerContractDetailsAndPlans($params){

        $firstPlanOfEveryCustomerSubQuery = "select(
                                                select 
                                                    contract_detail.contract_detail_id

                                                from contract
                                                    left join contract_detail on contract_detail.contract_id = contract.contract_id
                                                    left join product ON product.product_id = contract_detail.product_id

                                                where contract.customer_id = customer.customer_id
                                                and product.type = 'plan'
                                                order by contract_detail.date asc
                                                limit 1
                                            ) as contract_detail_ids
                                            from customer";
        $infoJoinSubQuery = "select
                                customer.customer_id,
                                customer.name,
                                customer.lastname,
                                contract.status as contractStatus,
                                contract_detail.*,
                                product.name as pName,
                                product.product_id as planProductId
                            from customer
                            left join contract on customer.customer_id = contract.customer_id
                            left join contract_detail on contract_detail.contract_id = contract.contract_id
                            left join product on product.product_id = contract_detail.product_id";
        $query = 
                "select
                    concat_ws('-', year(C.date), month(C.date)) as groupDate,
                    C.pName,
                    count(C.product_id) as cantAltasPorMes,
                    C.planProductId as product_id

                from(".$firstPlanOfEveryCustomerSubQuery.") as B
                join (".$infoJoinSubQuery.") as C 
                    on contract_detail_ids = C.contract_detail_id

                group by
                month(C.date),
                year(C.date),
                C.product_id
                order by
                groupDate desc,
                cantAltasPorMes desc";
            //  where C.contractStatus = 'active'

        $result = Yii::$app->db->createCommand($query);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $result->queryAll(),
            'pagination' => false,
        ]);
            
        return $dataProvider;       
    }

    public function findCustomersPerPlanPerMonth($params){


        $dateOfSearch = $params['year_month'];
        $completeDate = $params['year_month'].'-00';

        $firstPlanOfEveryCustomerSubQuery = "select(
                                                select 
                                                    contract_detail.contract_detail_id

                                                from contract
                                                    left join contract_detail on contract_detail.contract_id = contract.contract_id
                                                    left join product ON product.product_id = contract_detail.product_id

                                                where contract.customer_id = customer.customer_id
                                                and product.type = 'plan'
                                                order by contract_detail.date asc
                                                limit 1
                                            ) as contract_detail_ids
                                            from customer";
        $infoJoinSubQuery = "select
                                customer.customer_id,
                                customer.code,
                                customer.name,
                                customer.lastname,
                                contract.status as contractStatus,
                                contract_detail.*,
                                product.name as pName,
                                product.product_id as planProductId
                            from customer
                            left join contract on customer.customer_id = contract.customer_id
                            left join contract_detail on contract_detail.contract_id = contract.contract_id
                            left join product on product.product_id = contract_detail.product_id";
        $query = 
                "select
                    CONCAT_WS(' - ', CONCAT_WS(' ', C.name, C.lastname), C.code) as fullName,
                    C.pName,
                    C.planProductId as product_id,
                    C.date as detailDate,
                    C.contractStatus as contractStatus,
                    C.status as detailStatus, 
                    C.name, 
                    C.lastname, 
                    C.code,
                    C.customer_id,
                    C.contract_id

                from(".$firstPlanOfEveryCustomerSubQuery.") as B
                join (".$infoJoinSubQuery.") as C 
                    on contract_detail_ids = C.contract_detail_id
                where C.product_id = ".$params['product_id']."
                and YEAR(C.date) = YEAR('".$completeDate."')
                and MONTH(C.date) = MONTH('".$completeDate."')
                ";
            //  where C.contractStatus = 'active'

        $result = Yii::$app->db->createCommand($query);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $result->queryAll(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
            
        return $dataProvider;       
    }

}