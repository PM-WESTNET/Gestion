<?php

namespace app\modules\accounting\models\search;
use app\modules\accounting\models\Account;
use app\modules\accounting\models\AccountMovement;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 4/08/15
 * Time: 16:10
 */
class AccountSearch extends AccountMovement
{
    public $company_id;
    public $account_id;

    // Fechas
    public $toDate;
    public $fromDate;

    // Cuentas
    public $account_id_from;
    public $account_id_to;

    // Estados de movimiento
    public $statuses;

    public $totalDebit;
    public $totalCredit;

    private $innerWhere = "";
    private $innerParams = [];
    private $outerWhere = "";
    private $outerParams = [];

    public function rules()
    {
        $statuses = ['draft','closed'];

        return [
            [['account_id_from', 'account_id_to', 'company_id', 'account_id'], 'integer'],
            [['toDate', 'fromDate'], 'safe'],
            [['toDate', 'fromDate'], 'default', 'value'=>null],
            [['status'], 'in', 'range' => $statuses],
            ['statuses', 'each', 'rule' => ['in', 'range' => $statuses]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'statuses' => Yii::t('app', 'Statuses'),
            'fromDate' => Yii::t('app', 'From Date'),
            'toDate' => Yii::t('app', 'To Date'),
            'account_id_from' => Yii::t('accounting', 'Account Id From'),
            'account_id_to' => Yii::t('accounting', 'Account Id To'),
            'account_id' => Yii::t('accounting', 'Includes account'),
        ]);
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function init()
    {

        parent::init();
        $this->account_id_from = null;
        $this->account_id_to = null;

    }


    /**
     * Busqueda regular
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {

        $this->load($params);

        // Consulta del listado
        $sql = "SELECT @@ " .
            "FROM account INNER JOIN account AS account_parent ON account.lft BETWEEN account_parent.lft AND account_parent.rgt ".
            "LEFT JOIN ( " .
            "  SELECT ami.account_id, sum(ami.debit) as debit, sum(ami.credit) as credit FROM account_movement_item as ami " .
            "    LEFT JOIN account_movement ON account_movement.account_movement_id = ami.account_movement_id " .
            "    WHERE 1=1 %s" .
            "    GROUP BY ami.account_id " .
            ") ami ON account.account_id = ami.account_id " .
            "WHERE 1=1 %s ";

        $qListado = str_replace('@@', "account.account_id, account.code, CONCAT( REPEAT(' ', COUNT(account_parent.name) - 1), account.name) as name, ".
            "sum(DISTINCT coalesce(ami.debit,0)) debit, sum(DISTINCT coalesce(ami.credit,0)) credit", $sql);

        $qGroup = "GROUP BY account.code ".
            "ORDER BY account.lft";

        // Consulta de totales
        $qCantidad = str_replace('@@', 'COUNT(distinct account.code)', $sql);

        // Consulta de cantidad
        $qTotales = 'SELECT sum(debit) as debit, sum(credit) as credit FROM (' .
                        str_replace('@@', 'coalesce(ami.debit, 0)  AS debit, coalesce(ami.credit, 0) AS credit', $sql) .
                      $qGroup . " ) a";

        // Filtro las cuentas
        $this->filterAccount();

        //Estado/s de factura
        $this->filterStatus();

        //Fechas
        $this->filterDates();

        // Company
        $this->filterCompany();

        $this->filterByAccount();

        $parameters = array_merge($this->innerParams, $this->outerParams);

        $cantidad = Yii::$app->db->createCommand(sprintf($qCantidad, $this->innerWhere, $this->outerWhere), $parameters)
            ->queryScalar();

        $totales = Yii::$app->db->createCommand(sprintf($qTotales, $this->innerWhere, $this->outerWhere ), $parameters)
            ->queryOne();

        $this->totalDebit = $totales['debit'];
        $this->totalCredit = $totales['credit'];

        $sqlProvider = new SqlDataProvider([
            'sql' => sprintf($qListado, $this->innerWhere, $this->outerWhere) . $qGroup,
            'params' => $parameters,
            'pagination' => [
	            'pageSize' => 0,
	        ],
           'totalCount' => $cantidad,
        ]);

        return $sqlProvider;
    }

    /**
     * Aplica filtro a CUENTAS con una sola.
     * @param ActiveQuery $query
     */
    private function filterByAccount(){

        if(!empty($this->account_id)){
            $account = Account::findOne($this->account_id);
            $this->outerWhere .= " AND account.lft >= :left  AND  account.rgt <= :rgt";
            $this->outerParams[':left'] = $account->lft;
            $this->outerParams[':rgt'] = $account->rgt;
        }
    }

    /**
     * Aplica filtro a CUENTAS.
     * @param ActiveQuery $query
     */
    private function filterAccount(){

        if(!empty($this->account_id_from)){
            $this->outerWhere .= " AND account.lft >= :account_id_from ";
            $this->outerParams[':account_id_from'] = $this->account_id_from;
        }

        if(!empty($this->account_id_to)){
            $this->outerWhere .= " AND account.rgt <= :account_id_to ";
            $this->outerParams[':account_id_to'] = $this->account_id_to;
        }
    }

    /**
     * Aplica filtro a estado. Si statuses esta definido, aplica una condicion
     * "in". Sino aplica un "=" con status
     * @param ActiveQuery $query
     */
    private function filterStatus(){

        if(!empty($this->statuses)){
            $this->innerWhere .= " AND account_movement.status in(:statuses) ";
            $this->innerParams[':statuses'] = implode(',', $this->statuses );
        } else {
            if($this->status!==null) {
                $this->innerWhere .= " AND account_movement.status = :status ";
                $this->innerParams[':status'] = $this->status;
            }
        }
    }

    /**
     * Agrega queries para filtrar por fechas
     * @param type $query
     */
    private function filterDates()
    {
        if(!empty($this->fromDate)){
            $this->innerWhere .= 'AND account_movement.date>= :fromDate';
            $this->innerParams[':fromDate'] = Yii::$app->formatter->asDate($this->fromDate, 'yyyy-MM-dd');
        }

        if(!empty($this->toDate)){
            $this->innerWhere .= ' AND account_movement.date<= :toDate';
            $this->innerParams[':toDate'] = Yii::$app->formatter->asDate($this->toDate, 'yyyy-MM-dd');
        }
    }

    /**
     * Agrega queries para filtrar por fechas
     * @param type $query
     */
    private function filterCompany()
    {
        if(!empty($this->company_id)){
            $this->innerWhere .= ' AND (account_movement.company_id = :company_id OR account_movement.company_id IS NULL)';
            $this->innerParams[':company_id'] = $this->company_id;
        }
    }
}
