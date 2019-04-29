<?php

namespace app\modules\ticket\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\ticket\models\Ticket;

/**
 * TicketSearch represents the model behind the search form about `app\modules\agenda\models\Ticket`.
 */
class TicketSearch extends Ticket {

    const SCENARIO_WIDE_SEARCH = 'wideSearch';
    const SCENARIO_ACTIVE_SEARCH = 'activeSearch';

    public $search_text;
    public $user_id;
    public $customer;
    public $document;
    public $assignations;
    public $start_date_label;

    public function init() {
        parent::init();
    }

    public function rules() {
        return [
            [['ticket_id'], 'integer'],
            [['title', 'start_date', 'start_date', 'finish_date', 'status_id', 'type_id', 'customer_id', 'color_id', 'number', 'customer', 'document', 'assignations'], 'safe', 'on' => 'wideSearch'],
            [['title', 'start_date', 'type_id', 'customer_id', 'color_id', 'number'], 'safe', 'on' => 'activeSearch'],
            [['search_text'], 'safe'],
        ];
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
                return $query->from(DbHelper::getDbName(Yii::$app->db) . '.' . \app\modules\sale\models\Customer::tableName());
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

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
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
            'type_id' => $this->type_id,
            'color_id' => $this->color_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['like', 'number', $this->number]);
        $query->andFilterWhere(['like', 'customer.name', $this->customer]);
        $query->andFilterWhere(['like', 'customer.document_number', $this->document]);
        $query->andFilterWhere(['like', 'user.username', $this->assignations]);

        if (empty($params['sort'])) {
            $query->orderBy([
                'start_datetime' => SORT_DESC
            ]);
        }

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

}
