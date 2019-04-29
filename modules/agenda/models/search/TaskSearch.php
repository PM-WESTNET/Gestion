<?php

namespace app\modules\agenda\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\agenda\models\Task;

/**
 * TaskSearch represents the model behind the search form about `app\modules\agenda\models\Task`.
 */
class TaskSearch extends Task {

    public $search_text;
    public $user_id;
    public $create_option;
    public $user_option;

    public function init() {
        parent::init();
        $this->priority = null;
        $this->status_id = null;
    }

    public function rules() {
        return [
            [['task_id'], 'integer'],
            [['name', 'date', 'status_id', 'task_type_id', 'priority', 'category_id', 'creator_id', 'user_id'], 'safe'],
            [['search_text', 'create_option', 'user_option'], 'safe'],
        ];
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params) {
        $query = Task::find();

        //$query->andWhere(['parent_id' => null]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'task_id' => $this->task_id,
            'priority' => $this->priority,
            'date' => $this->date,
            'status_id' => $this->status_id,
            'task_type_id' => $this->task_type_id,
            'category_id' => $this->category_id,
            'creator_id' => $this->creator_id,
            'user_id' => $this->user_id
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    /**
     * @brief Arma una query con los datos de busqueda establecidos en $this desde el controller
     * @return type
     */
    public function searchAgenda() {

        $query = Task::find();

        $query->innerJoin('notifications');

        $query->andFilterWhere([
            'task_id' => $this->task_id,
            'priority' => $this->priority,
            'date' => $this->date,
            'status_id' => $this->status_id,
            'task_type_id' => $this->task_type_id,
            'category_id' => $this->category_id,
        ]);

        if ($this->create_option == 'others') {
            $query->andFilterWhere(['<>', 'creator_id', Yii::$app->user->id]);
        } elseif ($this->create_option == 'me') {
            $query->andFilterWhere(['=', 'creator_id', $this->creator_id]);
        }

        if ($this->user_option == 'others') {
           $query->andFilterWhere(['<>', 'user_id', Yii::$app->user->id]);
        } elseif ($this->user_option == 'me') {
            
            $query->andFilterWhere(['=', 'user_id', Yii::$app->user->id]);
        }


        //$query->andFilterWhere(['like', 'name', $this->name]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        error_log($query->createCommand()->getSql());
        return $dataProvider;
    }

    public function searchText($params) {

        $query = Task::find();

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

        $query->where([$operator, 'customer.name', $words, false])
                ->orWhere([$operator, 'lastname', $words, false])
                ->orWhere([$operator, 'email', $words, false])
                ->orWhere([$operator, 'customer.customer_id', $words, false])
                ->orWhere([$operator, 'document_number', $words, false])
                ->orWhere([$operator, 'phone', $words, false])
                ->orWhere([$operator, 'address', $words, false]);

        //Busqueda en profiles
        $query->innerJoinWith('customerProfiles', false);

        //Profiles habilitados para busqueda
        $profileClasses = \app\modules\agenda\models\Task::getSearchableProfileClasses();
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
            'query' => \app\modules\agenda\models\TaskHasCategory::find()->where(['customer_id' => $customer_id])->orderBy(['date_updated' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $dataProvider;
    }

    public function searchFlex($params = null) {

        $query = Task::find();

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

        $query->where([$operator, 'customer.name', $words, false])
                ->orWhere([$operator, 'lastname', $words, false])
                ->orWhere([$operator, 'email', $words, false])
                ->orWhere([$operator, 'document_number', $words, false])
                ->orWhere([$operator, 'phone', $words, false])
                ->orWhere([$operator, 'address', $words, false]);


        //Busqueda en profiles
        $query->joinWith('customerProfiles', false);

        //Profiles habilitados para busqueda
        $profileClasses = \app\modules\agenda\models\Task::getSearchableProfileClasses();
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
