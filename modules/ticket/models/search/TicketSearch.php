<?php

namespace app\modules\ticket\models\search;

use app\modules\ticket\models\Status;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\ticket\models\Ticket;
use app\components\helpers\DbHelper;
use app\modules\sale\models\Customer;
use yii\db\Query;
use yii\db\Expression;

/**
 * TicketSearch represents the model behind the search form about `app\modules\agenda\models\Ticket`.
 */
class TicketSearch extends Ticket {

    const SCENARIO_WIDE_SEARCH = 'wideSearch';
    const SCENARIO_ACTIVE_SEARCH = 'activeSearch';

    public $search_text;
    public $user_id;
    public $customer;
    public $customer_number;
    public $customer_id;
    public $document;
    public $assignations;
    public $start_date_label;
    public $ticket_management_qty;
    public $created_by;
    public $close_from_date;
    public $close_to_date;
    public $date_from_start_contract;
    public $date_to_start_contract;
    public $date_from_start_task;
    public $date_to_start_task;

    
    public $categories;
    public $start_date_from;
    public $start_date_to;
    public $show_all;

    public function init() {
        parent::init();
    }

    public function rules() {
        return [
            [['ticket_id'], 'integer'],
            [['show_all'], 'boolean'],
            [['discounted'], 'string'],
            [['title', 'start_date', 'start_date', 'finish_date', 'status_id', 'customer_id', 'color_id', 'category_id', 'number', 'customer', 'document',
                    'assignations', 'customer_number','date_from_start_contract', 'date_to_start_contract' ,'date_from_start_task','date_to_start_task', 'discounted'], 
                'safe', 'on' => 'wideSearch'
            ],
            [['title', 'start_date', 'customer_id', 'color_id', 'number', 'customer_number', 'date_from_start_contract', 'date_to_start_contract', 'date_from_start_task','date_to_start_task', 'discounted' ], 
                'safe', 'on' => 'activeSearch'],
            [['search_text', 'ticket_management_qty', 'close_from_date', 'close_to_date', 'category_id', 'categories', 'customer_id', 'created_by', 'start_date_from', 'start_date_to', 'status_id', 'assignations', 'show_all'], 'safe'],
        ];
    }
    
    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), [
            'document' => Yii::t('app', 'Document Number'),
            'customer' => Yii::t('app', 'Customer'),
            'customer_number' => Yii:: t('app', 'Customer Number'),
            'ticket_management_qty' => Yii::t('app', 'Ticket management quantity'),
            'created_by' => Yii::t('app', 'Created by'),
            'start_date_from' => Yii::t('app', 'Start ticket date from'),
            'start_date_to' => Yii::t('app', 'Start ticket date to'),
            'date_from_start_contract' => Yii::t('app', 'Start contract date from'),
            'date_to_start_contract' => Yii::t('app', 'Start contract date to'),
            'date_from_start_task' => Yii::t('app', 'Start task date from'),
            'date_to_start_task' => Yii::t('app', 'Start task date to'),

            'show_all' => Yii::t('app','Show All')
        ]);
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Busca en ticket segun los datos que vengan desde Grids
     * @param type $params
     * @return ActiveDataProvider
     */
    public function search($params) {    
        
        // var_dump($params);
        // die();

        $query = Ticket::find();

        $query->joinWith([
            'customer' => function($query) {
                return $query->from(DbHelper::getDbName(Yii::$app->db) . '.' . Customer::tableName());
            },
            'users' => function($query) {
                $userTableName = $this->userModelClass;
                return $query->from(DbHelper::getDbName(Yii::$app->db) . '.' . $userTableName::tableName());
            }
        ]);
        
        $query->innerJoin( DbHelper::getDbName(Yii::$app->db) . '.contract c', 'c.customer_id = customer.customer_id');

        $query->innerJoin( DbHelper::getDbName(Yii::$app->dbagenda) . '.task t', 't.task_id = ticket.task_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['customer'] = [
            'asc' => ['customer.name' => SORT_ASC],
            'desc' => ['customer.name' => SORT_DESC],
        ];

        $this->load($params);

        if($this->ticket_management_qty) {
            $query
                ->leftJoin('ticket_management tm', 'tm.ticket_id = ticket.ticket_id')
                ->where(['not',['tm.ticket_management_id' => null]])
                ->groupBy('ticket_id')
                ->having("count(tm.ticket_id) = $this->ticket_management_qty");
        }

        //Date Ranges
        if (!is_null($this->start_date) && strpos($this->start_date, ' al ') !== false) {
            list($start_date, $end_date) = explode(' al ', $this->start_date);

            $start_date = Yii::$app->formatter->asDate($start_date, 'yyyy-MM-dd');
            $end_date = Yii::$app->formatter->asDate($end_date, 'yyyy-MM-dd');

            $query->andFilterWhere(['between', 'start_date', $start_date, $end_date]);
            $this->start_date = $this->start_date;
        }

        if (!is_null($this->finish_date) && strpos($this->finish_date, ' al ') !== false) {
            list($start_date, $end_date) = explode(' al ', $this->finish_date);
            
            $start_date = Yii::$app->formatter->asDate($start_date, 'yyyy-MM-dd');
            $end_date = Yii::$app->formatter->asDate($end_date, 'yyyy-MM-dd');
            
            $query->andFilterWhere(['between', 'finish_date', $start_date, $end_date]);
            $this->finish_date = $this->finish_date;
        }

        //Rango de fecha start date
        if($this->start_date_from) {
            $query->andFilterWhere(['>=', 'start_date', $this->start_date_from]);
        }

        if($this->start_date_to) {
            $query->andFilterWhere(['<=', 'start_date', $this->start_date_to]);
        }

        $query->andFilterWhere([
            'ticket_id' => $this->ticket_id,
            'ticket.status_id' => $this->status_id,
            'color_id' => $this->color_id,
            'ticket.customer_id' => $this->customer_id,
        ]);

        if($this->category) {
            $query->andFilterWhere(['category_id' => $this->category_id]);
        }

        if($this->categories) {
            $query->andFilterWhere(['in','ticket.category_id', $this->categories]);
        }

        // Si no filtramos por ninguna categoria en especifico me fijo si hay categorias definidas para mostrar en el
        // panel de tickets y filtro por ellas
        if($this->scenario === self::SCENARIO_WIDE_SEARCH && empty($this->category) && empty($this->categories)){
            if (isset(Yii::$app->params['tickets_categories_showed']) && !empty(Yii::$app->params['tickets_categories_showed'])) {
                $query->andWhere(['IN', 'ticket.category_id', Yii::$app->params['tickets_categories_showed']]);
            }
        }

        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['like', 'number', $this->number]);
        $query->andFilterWhere(['like', 'customer.name', $this->customer]);
        $query->andFilterWhere(['like', 'customer.document_number', $this->document]);
        $query->andFilterWhere(['like', 'customer.code', $this->customer_number]);

        $query->andFilterWhere(['>=', 'c.from_date', $this->date_from_start_contract]);
        $query->andFilterWhere(['<=', 'c.from_date', $this->date_to_start_contract]);

        if($this->date_from_start_task){
            $query->andFilterWhere(['>=', 't.date', $this->date_from_start_task]);
        }

        if($this->date_to_start_task){
            $query->andFilterWhere(['<=', 't.date', $this->date_to_start_task]);
        }

        if (!empty($this->discounted)){
            Yii::info($this->discounted);
            if ($this->discounted == 'undiscounted'){
                $query->andWhere(['IS', 'discounted', null]);
            }else {
                $query->andWhere(['discounted' => '1']);
            }
        }

        if($this->assignations) {
            $query->leftJoin('assignation assig', 'assig.ticket_id = ticket.ticket_id')
                ->andFilterWhere(['assig.user_id' => $this->assignations]);
        }

        if($this->created_by) {
            $query->andFilterWhere(['ticket.user_id' => $this->created_by]);
        }

        if($this->user_id && !(boolean)$this->show_all) {
            $query->andFilterWhere(['like', 'user.id', $this->user_id]);
        }
        
        if (empty($params['sort'])) {
            $query->orderBy([
                'start_datetime' => SORT_DESC
            ]);
        }

        /**
         * Esto lo hacemos para limpiar los paneles de tickets erroneos cerrados por sistema
         */
        $err_status = Status::findOne(['name' => 'Cerrado por sistema']);

        if ($err_status) {
            $query->andWhere(['<>','ticket.status_id', $err_status->status_id]);
        }

        $query->distinct();
        $dataProvider->query = $query;

        return $dataProvider;
    }

    public function searchText($params) {

        $query = Ticket::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['customer_id' => SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $searchHelper = new \app\components\helpers\SearchStringHelper();
        $searchHelper->string = $this->search_text;

        //Separamos las palabras de busqueda
        $words = $searchHelper->getSearchWords('%{word}%');

        $operator = 'like';

        $query->where([$operator, 'customer.title', $words, false])
                ->orWhere([$operator, 'lastname', $words, false])
                ->orWhere([$operator, 'email', $words, false])
                ->orWhere([$operator, 'customer.customer_id', $words, false])
                ->orWhere([$operator, 'document_number', $words, false])
                ->orWhere([$operator, 'phone', $words, false])
                ->orWhere([$operator, 'address', $words, false]);

        //Busqueda en profiles
        $query->innerJoinWith('customerProfiles', false);

        //Profiles habilitados para busqueda
        $profileClasses = \app\modules\agenda\models\Ticket::getSearchableProfileClasses();
        foreach ($profileClasses as $class) {

            /* El query debe ser armado asi para que funcione coorectamente. Pasando profile_class_id como parametro :profile_class_id no funciona.
             * Utilizando llamadas a orWhere o a sus variantes, no se concatena adecuadamente. Con LIKE no es posible agragar un array con el
             * query de la porcion AND porque es ignorado por Query */
            foreach ($words as $word)
                $query->orWhere('profile.value LIKE :word', [':word' => $word]);
        }

        return $dataProvider;
    }

    public static function getdataProviderCategories($customer_id = null) {
        $dataProvider = new ActiveDataProvider([
            'query' => \app\modules\agenda\models\TicketHasCategory::find()->where(['customer_id' => $customer_id])->orderBy(['date_updated' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $dataProvider;
    }

    public function searchFlex($params = null) {

        $query = Ticket::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['customer_id' => SORT_DESC]
            ]
        ]);

        if ($params != null && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $searchHelper = new \app\components\helpers\SearchStringHelper();
        $searchHelper->string = $this->search_text;

        //Separamos las palabras de busqueda
        $words = $searchHelper->getSearchWords('%{word}%');

        $operator = 'or like';

        $query->where([$operator, 'customer.title', $words, false])
                ->orWhere([$operator, 'lastname', $words, false])
                ->orWhere([$operator, 'email', $words, false])
                ->orWhere([$operator, 'document_number', $words, false])
                ->orWhere([$operator, 'phone', $words, false])
                ->orWhere([$operator, 'address', $words, false]);


        //Busqueda en profiles
        $query->joinWith('customerProfiles', false);

        //Profiles habilitados para busqueda
        $profileClasses = Ticket::getSearchableProfileClasses();
        foreach ($profileClasses as $class) {

            /* El query debe ser armado asi para que funcione coorectamente. Pasando profile_class_id como parametro :profile_class_id no funciona.
             * Utilizando llamadas a orWhere o a sus variantes, no se concatena adecuadamente. Con LIKE no es posible agragar un array con el
             * query de la porcion AND porque es ignorado por Query */
            foreach ($words as $word)
                $query->orWhere('profile.value LIKE :word', [':word' => $word]);
        }

        return $dataProvider;
    }

    public function searchExternalTicket()
    {
        $query = Ticket::find();
        $query
            ->select('ticket.*')
            ->leftJoin('assignation a', 'ticket.ticket_id = a.ticket_id')
            ->leftJoin('status s', 'ticket.status_id = s.status_id')
            ->where('a.external_id is not null and s.is_open = 1')
        ;
        return $query->all();
    }

    public function searchClosedByPeriodAndStatus($params) {
        $query = Ticket::find();

        $this->load($params);

        $query->innerJoin('status st', 'st.status_id = ticket.status_id');

        $query->andWhere(['st.is_open' => 1]);

        if($this->categories) {
            $query->andWhere(['in', 'category_id', $this->categories]);
        } else {
            $query->andWhere(['category_id' => $this->category_id]);
        }

        if (!empty($this->close_from_date)) {
            $query->andFilterWhere(['>=', 'start_datetime', strtotime(Yii::$app->formatter->asDate($this->close_from_date, 'yyyy-MM-dd'))]);
        }

        if (!empty($this->close_to_date)) {
            $query->andFilterWhere(['<', 'start_datetime', (strtotime(Yii::$app->formatter->asDate($this->close_to_date, 'yyyy-MM-dd')) + 86400)]);
        }

        return $query;
    }

    /**
     * Devuelve registros para el reporte.
     */
    public function searchReport($params)
    {
        $this->load($params);

        $query = new Query();
        $query->select([new Expression('date_format(start_date, \'%Y-%m\') AS periodo'),new Expression('count(*) as qty')])
            ->from('ticket');

        if($this->assignations) {
            $query->leftJoin('assignation assig', 'assig.ticket_id = ticket.ticket_id')
                ->andFilterWhere(['assig.user_id' => $this->assignations]);
        }

        if($this->ticket_management_qty) {
            $ticket_managements_ids = (new Query)
                ->select('tm.ticket_id')
                ->from('ticket_management tm')
                ->groupBy('tm.ticket_id')
                ->having("count(tm.ticket_id) = $this->ticket_management_qty");

            $query->andFilterWhere(['in', 'ticket.ticket_id', $ticket_managements_ids]);
        }

        if($this->start_date_from){
            $query->andWhere(['>=','start_date', $this->start_date_from]);
        }

        if($this->start_date_to) {
            $query->andWhere(['<=', 'start_date', $this->start_date_to]);
        }

        if($this->status_id) {
            $query->andWhere(['status_id' => $this->status_id]);
        }

        if($this->created_by) {
            $query->andFilterWhere(['ticket.user_id' => $this->created_by]);
        }

        if($this->user_id) {
            $query->andFilterWhere(['like', 'user.id', $this->user_id]);
        }

        if($this->category_id) {
            $query->andFilterWhere(['category_id' => $this->category_id]);
        }

        $query->groupBy(new Expression('date_format(start_date, \'%Y-%m\')'));

        return $query->all(Yii::$app->dbticket);
    }
}
