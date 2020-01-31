<?php

namespace app\modules\afip\models\search;

use app\modules\afip\models\TaxesBook;
use app\modules\provider\models\ProviderBill;
use app\modules\sale\models\Bill;
use app\modules\sale\models\TaxRate;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 25/08/15
 * Time: 9:31
 * @property
 */
class TaxesBookSearch extends ProviderBill
{

    public $fromDate;
    public $toDate;
    public $period;
    public $status;
    public $totals;
    public $number;
    public $company_id;

    public $provider_id;
    public $employee_id;

    public $taxes_book_item_id;

    public $taxes_book_id;

    public $for_print = false;
    public $bill_types;

    public function rules()
    {
        return [
            [['provider_id', 'employee_id', 'company_id', 'taxes_book_item_id'], 'integer'],
            [['toDate', 'fromDate', 'for_print', 'bill_types', 'period', 'status', 'number'], 'safe'],
            [['fromDate'], 'default', 'value'=> date('Y-m-01')],
            [['toDate'], 'default', 'value'=> date('Y-m-t')]
        ];
    }

    public function attributeLabels()
    {
        return [
            'fromDate' => Yii::t('app', 'From Date'),
            'toDate' => Yii::t('app', 'To Date'),
            'provider_id' => Yii::t('app', 'Provider'),
            'company_id' => Yii::t('app', 'Company'),
            'period' => Yii::t('app', 'Period'),
            'status' => Yii::t('app', 'Status'),
            'number' => Yii::t('app', 'Number'),
        ];
    }

    public function search($params)
    {
        $this->load($params);

        $query = TaxesBook::find();

        if($this->company_id) {
            $query->andFilterWhere(['company_id' => $this->company_id]);
        }

        if($this->status) {
            $query->andFilterWhere(['status' => $this->status]);
        }

        if($this->period) {
            $query->andFilterWhere(['period' => $this->period .'-01']);
        }

        if($this->number) {
            $query->andFilterWhere(['number' => $this->number]);
        }

        if($this->type) {
            $query->andFilterWhere(['type' => $this->type]);
        }

        return $query;
    }

    public function findBillSale()
    {
        /** @var Query $query */
        $query = Bill::find();
        $query
            ->leftJoin('taxes_book_item tbi', 'bill.bill_id = tbi.bill_id')
            ->leftJoin('point_of_sale pos', 'pos.point_of_sale_id = bill.bill_id')
            ->where([
                'bill.status'=> 'closed',
                'bill.company_id'=> $this->company_id,
            ])
            ->andWhere(['=', new Expression("date_format(bill.date, '%Y%m')"),(new \DateTime($this->fromDate))->format('Ym')])
            ->andwhere(['is', 'tbi.bill_id', null])
            ->andWhere(['in', 'bill.bill_type_id', $this->bill_types])
            ->orderBy('pos.number, bill.number')
        ;
        return $query;
    }

    /**
     * Retorna los comprobantes para el Libro IVA Ventas
     *
     * @param $params
     * @return SqlDataProvider
     */
    public function findSale($params)
    {

        $this->load($params);

        $subQuery = 'SELECT distinct concat(c.lastname, \' \', c.name) AS business_name, c.document_number as tax_identification, b.date, bt.name AS bill_type, ' .
                    'b.number, b.amount,'.
                    '0.21 as pct, '.
                    'tbi.page, '.
                    '(if((b.amount = b.total), (b.amount / 1.21), b.amount)* bt.multiplier) AS net, ' .
                    '(b.total * bt.multiplier) AS total, ' .
                    '(if((b.amount = b.total), b.amount - (b.amount / 1.21), (b.total - b.amount))* bt.multiplier) AS tax ' .
                    'FROM bill as b ' .
                    'LEFT JOIN taxes_book_item tbi ON  b.bill_id = tbi.bill_id ' .
                    'LEFT JOIN customer as c ON b.customer_id = c.customer_id ' .
                    'LEFT JOIN bill_type as bt ON b.bill_type_id = bt.bill_type_id ' .
                    'LEFT JOIN (
                        SELECT
                          bill_id,
                          round((line_total / line_subtotal) - 1, 3) AS pct,
                          sum(line_subtotal)                         AS line_subtotal,
                          sum(line_total)                            AS line_total
                        FROM bill_detail
                        GROUP BY bill_id, round((line_total / line_subtotal) - 1, 3)
                    ) as bd ON b.bill_id = bd.bill_id ' .
                    'WHERE (b.ein is not null and b.ein <> \'\' or ((b.ein is null or b.ein = \'\') and bt.invoice_class_id is null ) ) AND %s AND bt.applies_to_sale_book = 1 ' .
                    'GROUP BY  c.name, c.document_number, b.date, concat( bt.name, \' - \', b.number), b.amount, if(bd.pct=0 or bd.pct is null,0.21, bd.pct), tbi.page ' .
                    'ORDER BY tbi.page, b.date desc';

        // Armo los filtros
        $params = [];
        $where = "1=1 ";

        $where .= "AND b.company_id =:company_id ";
        $params[':company_id'] = $this->company_id;

        $where .= 'AND tbi.taxes_book_id = :taxes_book_id ';
        $params[':taxes_book_id'] = $this->taxes_book_id;

        $subQuery = sprintf($subQuery, $where);

        // Traigo todas las columnas de impuestos para poder mostrarlas
        $columns = "";
        $taxesColumns = "";
        // Recorro los impuestos para cargar las columnas de cada uno
        foreach(TaxRate::find()->all() as $tax) {
            // Se agrega a la consulta general.
            $columns .= 'sum(CASE WHEN c.pct = ' . $tax->pct . ' THEN c.tax ELSE 0 END) as \'' . $tax->tax->name . ' ' . ($tax->pct*100) . '%\',';
            // Se agrega al totalizador
            $taxesColumns .= 'sum(`'. $tax->tax->name . ' ' . ($tax->pct*100) . '%`) as `'.$tax->tax->name . ' ' . ($tax->pct*100) .'%`,';
        }
        $columns = substr($columns, 0, strlen($columns)-1);
        $taxesColumns = substr($taxesColumns, 0, strlen($taxesColumns)-1);

        // Armo la consulta general
        $sql = 'SELECT business_name, tax_identification, date, bill_type, number, amount, page, sum(net) as net, sum(total) as total,' .$columns. ' FROM (' . $subQuery . ' ) c ' .
                'GROUP BY business_name, tax_identification, date, bill_type, number, amount ORDER BY page,date';

        // Consulta para traer totales
        $sqlTotals = "SELECT round(sum(net),2) as Subtotal, ".$taxesColumns.",round(sum(total),2) as Total FROM ( " . $sql . ") b";
        $this->totals = Yii::$app->db->createCommand($sqlTotals, $params)->queryOne();

        return new SqlDataProvider([
            'sql' => $sql,
            'params' => $params,
            'pagination' => [
                'pageSize' => 0,
            ],
        ]);
    }


    private function getBaseQueryBuy($total=false)
    {

        $subQuery = new Query();
        $subQuery
            ->select(['pb.provider_bill_id', 'tbi.taxes_book_item_id', 'p.name as business_name', 'p.tax_identification', 'pb.date',
                            'bt.name AS bill_type', 'pb.number', 'tbi.page', new Expression('(pb.net * bt.multiplier) as net'),
                            new Expression('(pb.total * bt.multiplier) as total'), 'tr.tax_rate_id', new Expression('(pbhtr.amount*bt.multiplier) as amount')])
            ->from('provider_bill pb')
            ->leftJoin('provider p', 'pb.provider_id = p.provider_id ' )
            ->leftJoin('bill_type AS bt', 'pb.bill_type_id = bt.bill_type_id ' )
            ->leftJoin('provider_bill_has_tax_rate pbhtr', 'pb.provider_bill_id = pbhtr.provider_bill_id ' )
            ->leftJoin('tax_rate AS tr', 'pbhtr.tax_rate_id = tr.tax_rate_id ' )
            ->leftJoin('taxes_book_item tbi', 'pb.provider_bill_id = tbi.provider_bill_id ' )
            ->where('pb.status = \'closed\' ')
            ->andWhere(['pb.company_id' => $this->company_id])
            ->andWhere(['bt.applies_to_buy_book' => 1])

        ;
        if(!empty($this->bill_types)){
            $subQuery->andWhere(['in', 'pb.bill_type_id', $this->bill_types]);
        }

        if(!empty($this->fromDate)){
            $subQuery->andWhere(['>=', 'pb.date', Yii::$app->formatter->asDate($this->fromDate, 'yyyy-MM-dd')]);
        }

        if(!empty($this->toDate)){
            $subQuery->andWhere(['<=', 'pb.date', Yii::$app->formatter->asDate($this->toDate, 'yyyy-MM-dd')]);
        }

        if (!empty($this->provider_id)) {
            $subQuery->andWhere(['=', 'pb.provider_id', $this->provider_id]);
        }

        if (!empty($this->taxes_book_id) &&  !$this->for_print && !$total) {
            $subQuery->andWhere(['or', 'tbi.taxes_book_id is null', 'tbi.taxes_book_id = :taxes_book_id']);
        } else if ($total || (!empty($this->taxes_book_id) &&  $this->for_print )) {
            $subQuery->andWhere('tbi.taxes_book_id= :taxes_book_id');
        }

        $subQuery->addParams([':taxes_book_id' => $this->taxes_book_id]);


        $mainQuery = new Query();
        $mainQuery
            ->select(['provider_bill_id', 'taxes_book_item_id', 'business_name',
            'tax_identification', 'date', 'page', 'bill_type', 'number', 'net', 'total'])
            ->from(['c'=>$subQuery])
            ->groupBy(['business_name', 'tax_identification', 'date', 'page', 'bill_type', 'number', 'net', 'total'])
            ->orderBy(['page'=>SORT_ASC, 'date'=>SORT_ASC])
        ;

        // Recorro los impuestos para cargar las columnas de cada uno
        foreach(TaxRate::find()->all() as $tax) {
            $mainQuery->addSelect(new Expression('sum(CASE WHEN tax_rate_id = ' . $tax->tax_rate_id . ' THEN amount ELSE 0 END) as \'' . $tax->tax->name . ' ' . ($tax->pct*100) . '%\''));
        }


        // Armo la consulta general
        if ($total) {
            $query = new Query();
            $query
                ->select(new Expression('round(sum(net),2) as Subtotal'))
                ->from(['b'=> $mainQuery]);

                foreach(TaxRate::find()->all() as $tax) {
                    $query->addSelect(new Expression('sum(`'. $tax->tax->name . ' ' . ($tax->pct*100) . '%`) as `'.$tax->tax->name . ' ' . ($tax->pct*100) .'%`'));
                }
                $query->addSelect(new Expression('round(sum(total),2) as Total'));

            return $query;
        } else {
            return $mainQuery;
        }
    }

    private function getBaseQueryEmployeeBuy($total=false)
    {

        $subQuery = new Query();
        $subQuery
            ->select(['pb.employee_bill_id', 'tbi.taxes_book_item_id', 'CONCAT(p.name, " ", p.lastname) as fullName', 'p.document_number', 'pb.date',
                'bt.name AS bill_type', 'pb.number', 'tbi.page', new Expression('(pb.net * bt.multiplier) as net'),
                new Expression('(pb.total * bt.multiplier) as total'), 'tr.tax_rate_id', new Expression('(pbhtr.amount*bt.multiplier) as amount')])
            ->from('employee_bill pb')
            ->leftJoin('employee p', 'pb.employee_id = p.employee_id ' )
            ->leftJoin('bill_type AS bt', 'pb.bill_type_id = bt.bill_type_id ' )
            ->leftJoin('employee_bill_has_tax_rate pbhtr', 'pb.employee_bill_id = pbhtr.employee_bill_id ' )
            ->leftJoin('tax_rate AS tr', 'pbhtr.tax_rate_id = tr.tax_rate_id ' )
            ->leftJoin('taxes_book_item tbi', 'pb.employee_bill_id = tbi.employee_bill_id ' )
            ->where('pb.status = \'closed\' ')
            ->andWhere(['pb.company_id' => $this->company_id])
            ->andWhere(['bt.applies_to_buy_book' => 1])

        ;
        if(!empty($this->bill_types)){
            $subQuery->andWhere(['in', 'pb.bill_type_id', $this->bill_types]);
        }

        if(!empty($this->fromDate)){
            $subQuery->andWhere(['>=', 'pb.date', Yii::$app->formatter->asDate($this->fromDate, 'yyyy-MM-dd')]);
        }

        if(!empty($this->toDate)){
            $subQuery->andWhere(['<=', 'pb.date', Yii::$app->formatter->asDate($this->toDate, 'yyyy-MM-dd')]);
        }

        if (!empty($this->employee_id)) {
            $subQuery->andWhere(['=', 'pb.employee_id', $this->employee_id]);
        }

        if (!empty($this->taxes_book_id) &&  !$this->for_print && !$total) {
            $subQuery->andWhere(['or', 'tbi.taxes_book_id is null', 'tbi.taxes_book_id = :taxes_book_id']);
        } else if ($total || (!empty($this->taxes_book_id) &&  $this->for_print )) {
            $subQuery->andWhere('tbi.taxes_book_id= :taxes_book_id');
        }

        $subQuery->addParams([':taxes_book_id' => $this->taxes_book_id]);


        $mainQuery = new Query();
        $mainQuery
            ->select(['employee_bill_id', 'taxes_book_item_id', 'fullName',
                'document_number', 'date', 'page', 'bill_type', 'number', 'net', 'total'])
            ->from(['c'=>$subQuery])
            ->groupBy(['fullName', 'document_number', 'date', 'page', 'bill_type', 'number', 'net', 'total'])
            ->orderBy(['page'=>SORT_ASC, 'date'=>SORT_ASC])
        ;

        // Recorro los impuestos para cargar las columnas de cada uno
        foreach(TaxRate::find()->all() as $tax) {
            $mainQuery->addSelect(new Expression('sum(CASE WHEN tax_rate_id = ' . $tax->tax_rate_id . ' THEN amount ELSE 0 END) as \'' . $tax->tax->name . ' ' . ($tax->pct*100) . '%\''));
        }


        // Armo la consulta general
        if ($total) {
            $query = new Query();
            $query
                ->select(new Expression('round(sum(net),2) as Subtotal'))
                ->from(['b'=> $mainQuery]);

            foreach(TaxRate::find()->all() as $tax) {
                $query->addSelect(new Expression('sum(`'. $tax->tax->name . ' ' . ($tax->pct*100) . '%`) as `'.$tax->tax->name . ' ' . ($tax->pct*100) .'%`'));
            }
            $query->addSelect(new Expression('round(sum(total),2) as Total'));

            return $query;
        } else {
            return $mainQuery;
        }
    }


    /**
     * Retorna todos los comprobantes pagados dentro de los parametros seteados.
     *
     * @param $params
     * @return ActiveDataProvider
     */
    public function findBuyBills($params)
    {
        $this->load($params);
        $sql = $this->getBaseQueryBuy();

        return new ActiveDataProvider([
            'query' => $sql,
        ]);
    }

    /**
     * Retorna todos los comprobantes de empleados pagados dentro de los parametros seteados.
     *
     * @param $params
     * @return ActiveDataProvider
     */
    public function findBuyEmployeeBills($params)
    {
        $this->load($params);
        $sql = $this->getBaseQueryEmployeeBuy();

        return new ActiveDataProvider([
            'query' => $sql,
        ]);
    }


    /**
     * Retorna los totales de los comprobantes seleccionados y su respectivos impuestos.
     *
     * @param $params
     * @return array|bool
     */
    public function findTotals($params)
    {
        $this->load($params);

        $query = $this->getBaseQueryBuy(true);
        $query2 = $this->getBaseQueryEmployeeBuy(true);

        $query->union($query2);

        $masterQuery = new Query();
        $masterQuery
            ->select(new Expression('round(sum(Subtotal),2) as Subtotal'))
            ->from(['b'=> $query]);

        foreach(TaxRate::find()->all() as $tax) {
            $masterQuery->addSelect(new Expression('sum(`'. $tax->tax->name . ' ' . ($tax->pct*100) . '%`) as `'.$tax->tax->name . ' ' . ($tax->pct*100) .'%`'));
        }
        $masterQuery->addSelect(new Expression('round(sum(Total),2) as Total'));



        $this->totals = $masterQuery->one();
        return $this->totals;
    }

    public function findSaleTxt($params)
    {

        $this->load($params);

        $sql = "SELECT DISTINCT
                    trim(concat(c.lastname, ' ', c.name))       AS empresa,
                    bt.code                                     as tipo_comprobante,
                    pos.number                                  as punto_de_venta,
                    dt.code                                     as tipo_documento,
                    c.document_number                           AS numero_documento,
                    b.date,
                    bt.name                                     AS bill_type,
                    b.number as numero_comprobante,
                    b.amount,
                    tr.code                                     as tipo_de_iva,
                    ''                                          as numero_importacion,
                     'PES'                                      as codigo_moneda,
                     1                                          as tipo_de_cambio,
                     ' '                                        as codigo_operacion,
                     0                                          as otros_tributos,
                     0                                          as conceptos_no_incluido_neto,
                     0                                          as percepciones_no_categorizadas,
                     0                                          as exento,
                     0                                          as percepciones_nacionales,
                     0                                          as percepciones_iibb,
                     0                                          as percepciones_municipales,
                     0                                          as impuestos_internos,
                     1                                          as cantidad_iva,
                    round(if(b.total = b.amount, 0.21, (b.total / b.amount) - 1), 2)                               AS pct,
                    tbi.page,
                    (if((b.amount = b.total), (b.amount / 1.21), b.amount) * bt.multiplier)                        AS neto,
                    (b.total * bt.multiplier)                                                                      AS total,
                    (if((b.amount = b.total), b.amount - (b.amount / 1.21), (b.total - b.amount)) * bt.multiplier) AS impuesto_liquidado
                  FROM bill AS b
                    LEFT JOIN taxes_book_item tbi ON b.bill_id = tbi.bill_id
                    LEFT JOIN customer AS c ON b.customer_id = c.customer_id
                    LEFT JOIN bill_type AS bt ON b.bill_type_id = bt.bill_type_id
                    LEFT JOIN tax_rate tr on round(tr.pct,2) = round(if(b.total = b.amount, 0.21, (b.total / b.amount) - 1), 2)
                    LEFT JOIN document_type dt on c.document_type_id = dt.document_type_id
                    LEFT JOIN point_of_sale pos on b.company_id = pos.company_id
                  WHERE
                  (b.ein IS NOT NULL AND b.ein <> '' OR ((b.ein IS NULL OR b.ein = '') AND bt.invoice_class_id IS NULL)) AND 1 = 1
                AND b.company_id = :company_id AND tbi.taxes_book_id = :taxes_book_id
                AND bt.applies_to_sale_book = 1
                  GROUP BY c.name, c.document_number, b.date, concat(bt.name, ' - ', b.number), b.amount, tbi.page, tr.code
                  ORDER BY tbi.page, b.date DESC, b.number";

        $queryParams[':company_id'] = $this->company_id;
        $queryParams[':taxes_book_id'] = $this->taxes_book_id;

        return new SqlDataProvider([
            'sql' => $sql,
            'params' => $queryParams,
            'pagination' => [
                'pageSize' => 0,
            ],
        ]);
    }

    /**
     * @param $params
     * @return SqlDataProvider
     * Primer bloque de CASE WHEN : Es necesario que se tenga en cuenta los importes de los impuestos que no son iva en los campos que están destinado a ello
     * Segundo bloque de CASE WHEN : Se determina el importe del impuesto por cada tipo de iva
     * Tercer bloque de CASE WHEN : Se suma 1 por cada tipo de iva que está presente en el comprobante, ya que de esa manera se puede determinar la cantidad de alicuotas. (tener presente que por cada tipo de iva, se debe enviar una alicuota)
     * Cuarto bloque de CASE WHEN : Código de cada tipo de iva, en caso de que se incluya en el comprobante
     */
    public function findBuyTxt($params)
    {
        $this->load($params);

        $sql = "SELECT
                    date,
                    bill_type                                                                   as tipo_comprobante,
                    bill_type_name                                                              as nombre_tipo_comprobante,
                    number                                                                      as numero_comprobante,
                    0                                                                           as numero_importacion,
                    tipo_documento                                                              as tipo_documento,
                    tax_identification                                                          as numero_documento,
                    business_name                                                               as empresa,
                    ' '                                                                         as codigo_operacion,
                    total,  
                    SUM(conceptos_no_gravados)                                                  as conceptos_no_incluido_neto, 
                    0                                                                           as exento,
                    SUM(percepcion_iva)                                                         as percepciones_a_cuenta_iva, 
                    SUM(retencion_ganancias)                                                    as percepciones_a_cuenta_otros,
                    (SUM(ingresos_brutos) + SUM(percepcion_ingresos_brutos) + SUM(retencion_ingresos_brutos))  as iibb,
                    0                                                                           as municipales,
                    0                                                                           as internos,
                    'PES'                                                                       as codigo_moneda,
                    1                                                                           as tipo_de_cambio,
                    (SUM(cant_iva_105) + SUM(cant_iva_21) + SUM(cant_iva_27) + SUM(cant_iva_06) + SUM(cant_iva_05) + SUM(cant_iva_025)) as cantidad_iva,
                    (total - net - (SUM(ingresos_brutos) + SUM(percepcion_ingresos_brutos) + SUM(retencion_ingresos_brutos)) - SUM(retencion_ganancias) - SUM(conceptos_no_gravados) - SUM(percepcion_iva)) as credito_fiscal,
                    0                                                                           as otros_tributos,
                    0                                                                           as cuit_emisor,
                    ''                                                                          as emisor,
                    0                                                                           as iva_comision,
                    net                                                                         as neto,
                    code                                                                        as tipo_de_iva,
                    (total - net - (SUM(ingresos_brutos) + SUM(percepcion_ingresos_brutos) + SUM(retencion_ingresos_brutos)) - SUM(retencion_ganancias) - SUM(conceptos_no_gravados) - SUM(percepcion_iva)) as impuesto_liquidado,
                    SUM(code_iva_105)                                                           as code_iva_105,
                    SUM(code_iva_21)                                                            as code_iva_21,
                    SUM(code_iva_27)                                                            as code_iva_27,
                    SUM(code_iva_06)                                                            as code_iva_06,
                    SUM(code_iva_05)                                                            as code_iva_05,
                    SUM(code_iva_025)                                                           as code_iva_025,
                    SUM(iva_105)                                                                as iva_105,
                    SUM(iva_21)                                                                 as iva_21,
                    SUM(iva_27)                                                                 as iva_27,
                    SUM(iva_06)                                                                 as iva_06,
                    SUM(iva_05)                                                                 as iva_05,
                    SUM(iva_025)                                                                as iva_025,
                    SUM(net_iva_105)                                                            as net_iva_105,
                    SUM(net_iva_21)                                                             as net_iva_21,
                    SUM(net_iva_27)                                                             as net_iva_27,
                    SUM(net_iva_06)                                                             as net_iva_06,
                    SUM(net_iva_05)                                                             as net_iva_05,
                    SUM(net_iva_025)                                                            as net_iva_025,
                    page                                                                        AS page,
                    retencion_iva                                                               AS retencion_iva,
                    retencion_ingresos_brutos                                                   AS retencion_ingresos_brutos
                FROM (SELECT
                        pb.provider_bill_id,
                        tbi.taxes_book_item_id,
                        p.name                     AS business_name,
                        p.tax_identification,
                        CASE WHEN p.tax_condition_id = 3 THEN 96 ELSE 80 END as tipo_documento,
                        pb.date,
                        bt.code                    AS bill_type,
                        bt.name                    AS bill_type_name,
                        pb.number,
                        tbi.page,
                        (pb.net * bt.multiplier)       AS net,
                        (pb.total * bt.multiplier)     AS total,
                        tr.pct,
                        coalesce(tr.code, 5) as code,
                        (pbhtr.amount * bt.multiplier) AS amount,
                        
                        CASE WHEN tx.slug = 'ingresos-brutos' THEN pbhtr.amount ELSE 0 END as ingresos_brutos,
                        CASE WHEN tx.slug = 'cptos-no-grav' THEN pbhtr.amount ELSE 0 END as conceptos_no_gravados,
                        CASE WHEN tx.slug = 'percep-iva' THEN pbhtr.amount ELSE 0 END as percepcion_iva,
                        CASE WHEN tx.slug = 'percep-ing-b' THEN pbhtr.amount ELSE 0 END as percepcion_ingresos_brutos,
                        CASE WHEN tx.slug = 'retenc-iva' THEN pbhtr.amount ELSE 0 END as retencion_iva,
                        CASE WHEN tx.slug = 'retenc-ing-b' THEN pbhtr.amount ELSE 0 END as retencion_ingresos_brutos,
                        CASE WHEN tx.slug = 'retenc-gan' THEN pbhtr.amount ELSE 0 END as retencion_ganancias,
                        CASE WHEN tx.slug = 'iva-otros' THEN pbhtr.amount ELSE 0 END as iva_otros,
                        
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.105' THEN pbhtr.amount ELSE 0 END as iva_105,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.21' THEN pbhtr.amount ELSE 0 END as iva_21,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.27' THEN pbhtr.amount ELSE 0 END as iva_27,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.06' THEN pbhtr.amount ELSE 0 END as iva_06,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.05' THEN pbhtr.amount ELSE 0 END as iva_05,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.025' THEN pbhtr.amount ELSE 0 END as iva_025,
                        
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.105' THEN pbhtr.net ELSE 0 END as net_iva_105,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.21' THEN pbhtr.net ELSE 0 END as net_iva_21,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.27' THEN pbhtr.net ELSE 0 END as net_iva_27,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.06' THEN pbhtr.net ELSE 0 END as net_iva_06,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.05' THEN pbhtr.net ELSE 0 END as net_iva_05,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.025' THEN pbhtr.net ELSE 0 END as net_iva_025,
                        
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.105' THEN 1 ELSE 0 END as cant_iva_105,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.21' THEN 1 ELSE 0 END as cant_iva_21,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.27' THEN 1 ELSE 0 END as cant_iva_27,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.06' THEN 1 ELSE 0 END as cant_iva_06,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.05' THEN 1 ELSE 0 END as cant_iva_05,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.025' THEN 1 ELSE 0 END as cant_iva_025,
                        
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.105' THEN tr.code ELSE 0 END as code_iva_105,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.21' THEN tr.code ELSE 0 END as code_iva_21,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.27' THEN tr.code ELSE 0 END as code_iva_27,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.06' THEN tr.code ELSE 0 END as code_iva_06,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.05' THEN tr.code ELSE 0 END as code_iva_05,
                        CASE WHEN tx.slug =  'iva' AND tr.pct = '0.025' THEN tr.code ELSE 0 END as code_iva_025
                                                
                      FROM provider_bill pb LEFT JOIN provider p ON pb.provider_id = p.provider_id
                        LEFT JOIN bill_type bt ON pb.bill_type_id = bt.bill_type_id
                        LEFT JOIN provider_bill_has_tax_rate pbhtr ON pb.provider_bill_id = pbhtr.provider_bill_id
                        LEFT JOIN tax_rate tr ON pbhtr.tax_rate_id = tr.tax_rate_id
                        LEFT JOIN tax tx ON tx.tax_id = tr.tax_id
                        LEFT JOIN taxes_book_item tbi ON pb.provider_bill_id = tbi.provider_bill_id
                      WHERE
                      ((pb.status = 'closed') AND (pb.company_id = :company_id)) AND (tbi.taxes_book_id = :taxes_book_id) AND bt.applies_to_buy_book = 1) c
                GROUP BY business_name, tax_identification, tipo_documento, date, page, bill_type, number, net, total
                ORDER BY page, date";

        $queryParams[':company_id'] = $this->company_id;
        $queryParams[':taxes_book_id'] = $this->taxes_book_id;

        return new SqlDataProvider([
            'sql' => $sql,
            'params' => $queryParams,
            'pagination' => [
                'pageSize' => 0,
            ],
        ]);
    }
}