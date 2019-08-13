<?php

namespace app\modules\ticket\models\search;

use app\modules\ticket\models\Status;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\ticket\models\Ticket;
use app\components\helpers\DbHelper;
use app\modules\sale\models\Customer;

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
    public $document;
    public $assignations;
    public $start_date_label;
    public $ticket_management_qty;

    public $close_from_date;
    public $close_to_date;

    public function init() {
        parent::init();
    }

    public function rules() {
        return [
            [['ticket_id'], 'integer'],
            [['title', 'start_date', 'start_date', 'finish_date', 'status_id', 'customer_id', 'color_id', 'category_id', 'number', 'customer', 'document', 'assignations', 'customer_number'], 'safe', 'on' => 'wideSearch'],
            [['title', 'start_date', 'customer_id', 'color_id', 'number', 'customer_number'], 'safe', 'on' => 'activeSearch'],
            [['search_text', 'ticket_management_qty', 'close_from_date', 'close_to_date', 'category_id'], 'safe'],
        ];
    }
    
    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), [
            'document' => Yii::t('app', 'Document Number'),
            'customer' => Yii::t('app', 'Customer'),
            'customer_number' => Yii:: t('app', 'Customer Number'),
            'ticket_management_qty' => Yii::t('app', 'Ticket management quantity'),
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
        $query = Ticket::find();

        $query->joinWith([
            'customer' => function($query) {
                return $query->from(DbHelper::getDbName(Yii::$app->db) . '.' . Customer::tableName());
            },
            'users' => function($query) {
                $userTableName = $this->userModelClass;
                return $query->from(DbHelper::getDbName(Yii::$app->db) . '.' . $userTableName::tableName());
            },
        ]);
        
         
            
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

        $query->andFilterWhere([
            'ticket_id' => $this->ticket_id,
            'status_id' => $this->status_id,
            'color_id' => $this->color_id,
            'category_id' => $this->category_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['like', 'number', $this->number]);
        $query->andFilterWhere(['like', 'customer.name', $this->customer]);
        $query->andFilterWhere(['like', 'customer.document_number', $this->document]);
        $query->andFilterWhere(['like', 'customer.code', $this->customer_number]);

        if($this->assignations) {
            $query->leftJoin('assignation assig', 'assig.ticket_id = ticket.ticket_id')
                ->andFilterWhere(['assig.user_id' => $this->assignations]);
        }

        if($this->user_id) {
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
            $query->andWhere(['<>','status_id', $err_status->status_id]);
        }


        $query->orderBy('ticket.status_id');

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

        $query->innerJoin('status st', 'st.status_id=ticket.status_id');

        $query->andWhere(['st.is_open' => 1]);
        $query->andWhere(['category_id' => $this->category_id]);

        if (!empty($this->close_from_date)) {
            $query->andFilterWhere(['>=', 'start_datetime', strtotime(Yii::$app->formatter->asDate($this->close_from_date, 'yyyy-MM-dd'))]);
        }

        if (!empty($this->close_to_date)) {
            $query->andFilterWhere(['<', 'start_datetime', (strtotime(Yii::$app->formatter->asDate($this->close_to_date, 'yyyy-MM-dd')) + 86400)]);
        }

        return $query;
    }
}
