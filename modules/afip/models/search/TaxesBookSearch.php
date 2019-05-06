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

    public $taxes_book_item_id;

    public $taxes_book_id;

    public $for_print = false;
    public $bill_types;

    public function rules()
    {
        return [
            [['provider_id', 'company_id', 'taxes_book_item_id'], 'integer'],
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
                    'b.number, b.amount, round(if(b.total=b.amount, 0.21, (b.total/b.amount) - 1 ),2) AS pct, tbi.page,'.
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
     * Retorna los totales de los comprobantes seleccionados y su respectivos impuestos.
     *
     * @param $params
     * @return array|bool
     */
    public function findTotals($params)
    {
        $this->load($params);

        $query = $this->getBaseQueryBuy(true);

        $this->totals = $query->one();
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
                    LEFT JOIN (
                                SELECT bill_id,
                                  round((line_total / line_subtotal) - 1, 3) AS pct,
                                  sum(line_subtotal)                         AS line_subtotal,
                                  sum(line_total)                            AS line_total
                                FROM bill_detail
                                GROUP BY bill_id, round((line_total / line_subtotal) - 1, 3)
                              ) AS bd ON b.bill_id = bd.bill_id
                    LEFT JOIN tax_rate tr on round(tr.pct,2) = round(if(b.total = b.amount, 0.21, (b.total / b.amount) - 1), 2)
                    LEFT JOIN document_type dt on c.document_type_id = dt.document_type_id
                    LEFT JOIN point_of_sale pos on b.company_id = pos.company_id
                  WHERE
                  (b.ein IS NOT NULL AND b.ein <> '' OR ((b.ein IS NULL OR b.ein = '') AND bt.invoice_class_id IS NULL)) AND 1 = 1
                AND b.company_id = :company_id AND tbi.taxes_book_id = :taxes_book_id
                AND bt.applies_to_sale_book = 1
                  GROUP BY c.name, c.document_number, b.date, concat(bt.name, ' - ', b.number), b.amount,
                    if(bd.pct = 0 OR bd.pct IS NULL, 0.21, bd.pct), tbi.page, tr.code
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

    public function findBuyTxt($params)
    {

        $this->load($params);

        $sql = "SELECT
                    date,
                    bill_type                                 as tipo_comprobante,
                    number                                    as numero_comprobante,
                    0                                         as numero_importacion,
                    tipo_documento                            as tipo_documento,
                    tax_identification                        as numero_documento,
                    business_name                             as empresa,
                    ' '                                       as codigo_operacion,
                    total,
                    0                                         as conceptos_no_incluido_neto,
                    0                                         as exento,
                    0                                         as percepciones_a_cuenta_iva,
                    0                                         as percepciones_a_cuenta_otros,
                    0                                         as iibb,
                    0                                         as municipales,
                    0                                         as internos,
                    'PES'                                     as codigo_moneda,
                    1                                         as tipo_de_cambio,
                    1                                         as cantidad_iva,
                    (total - net)                             as credito_fiscal,
                    0                                         as otros_tributos,
                    0                                         as cuit_emisor,
                    ''                                        as emisor,
                    0                                         as iva_comision,
                    net                                       as neto,
                    code                                      as tipo_de_iva,
                    (total -net)                              as impuesto_liquidado
                FROM (SELECT
                        pb.provider_bill_id,
                        tbi.taxes_book_item_id,
                        p.name                     AS business_name,
                        p.tax_identification,
                        CASE WHEN p.tax_condition_id = 3 THEN 96 ELSE 80 END as tipo_documento,
                        pb.date,
                        bt.code                    AS bill_type,
                        pb.number,
                        tbi.page,
                        (pb.net * bt.multiplier)       AS net,
                        (pb.total * bt.multiplier)     AS total,
                        tr.pct,
                        coalesce(tr.code, 5) as code,
                        (pbhtr.amount * bt.multiplier) AS amount
                      FROM provider_bill pb LEFT JOIN provider p ON pb.provider_id = p.provider_id
                        LEFT JOIN bill_type bt ON pb.bill_type_id = bt.bill_type_id
                        LEFT JOIN provider_bill_has_tax_rate pbhtr ON pb.provider_bill_id = pbhtr.provider_bill_id
                        LEFT JOIN tax_rate tr ON pbhtr.tax_rate_id = tr.tax_rate_id
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