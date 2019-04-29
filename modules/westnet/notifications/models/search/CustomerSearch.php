<?php

namespace app\modules\westnet\notifications\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\notifications\models\Notification;
use app\modules\sale\models\Customer;

/**
 * CustomerSearch represents the model behind the search form about `app\modules\sale\models\Customer`.
 */
class CustomerSearch extends Customer {

    public $search_text;
    public $toDate;

    public function rules() {
        return [
            [['customer_id', 'document_type_id'], 'integer'],
            [['name', 'lastname', 'document_number', 'sex', 'email', 'phone', 'address', 'status', 'customer_id', 'code'], 'safe'],
            [['search_text', 'toDate'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), [
            'toDate' => Yii::t('app', 'To Date'),
        ]);
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Busqueda para filtros
     * @param type $params
     * @return ActiveDataProvider
     */
    public function search($params) {
        
        $query = Customer::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'customer_id' => $this->customer_id,
            'code' => $this->code,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'lastname', $this->lastname])
                ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }

    /**
     * Busqueda con like (searchFlex busca con or like)
     * @param type $params
     * @return ActiveDataProvider
     */
    public function searchText($params) {

        $query = Customer::find();

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
        $query->joinWith('customerProfiles', false);


        //Profiles habilitados para busqueda
        $profileClasses = \app\modules\sale\models\Customer::getSearchableProfileClasses();
        foreach ($profileClasses as $class) {

            /* El query debe ser armado asi para que funcione coorectamente. Pasando profile_class_id como parametro :profile_class_id no funciona.
             * Utilizando llamadas a orWhere o a sus variantes, no se concatena adecuadamente. Con LIKE no es posible agragar un array con el
             * query de la porcion AND porque es ignorado por Query */
            foreach ($words as $word)
                $query->orWhere('profile.value LIKE :word', [':word' => $word]);
        }

        return $dataProvider;
    }

    public static function getdataProviderClasses($customer_id = null) {
        $dataProvider = new ActiveDataProvider([
            'query' => \app\modules\sale\models\CustomerHasClass::find()->where(['customer_id' => $customer_id])->orderBy(['date_updated' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $dataProvider;
    }

    /**
     * Busqueda con "or like"
     * @param type $params
     * @return ActiveDataProvider
     */
    public function searchFlex($params = null) {

        $query = Customer::find();

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
        $profileClasses = \app\modules\sale\models\Customer::getSearchableProfileClasses();
        foreach ($profileClasses as $class) {

            /* El query debe ser armado asi para que funcione coorectamente. Pasando profile_class_id como parametro :profile_class_id no funciona.
             * Utilizando llamadas a orWhere o a sus variantes, no se concatena adecuadamente. Con LIKE no es posible agragar un array con el
             * query de la porcion AND porque es ignorado por Query */
            foreach ($words as $word)
                $query->orWhere('profile.value LIKE :word', [':word' => $word]);
        }

        return $dataProvider;
    }

    public function searchDebtors($params) {
        $this->load($params);

        $qMethodPayment = (new Query())->select(['payment_method_id'])
                ->from('payment_method')
                ->where(['=', 'type', 'account']);


        $subQuery = (new Query())
                ->select(['sum(b.total * bt.multiplier) as amount'])
                ->from('bill b')
                ->leftJoin('bill_type bt', 'b.bill_type_id = bt.bill_type_id')
                ->where(['b.status' => 'closed'])
                ->andWhere(['=', 'b.customer_id', 'c.customer_id']);

        $masterSubQuery = (new Query())->select(['c.customer_id', 'c.name', 'c.phone', 'coalesce((' . $subQuery->createCommand()->getSql() . '), 0) - sum(coalesce(p.amount, 0)) as saldo'])
                ->from('customer c')
                ->leftJoin('payment p', 'c.customer_id = p.customer_id')
                ->leftJoin('payment_item pi', 'p.payment_id = pi.payment_id')
                ->where(['NOT IN', 'pi.payment_method_id', $qMethodPayment])
                ->groupBy(['c.customer_id', 'c.name', 'c.phone']);

        if (!empty($this->toDate)) {
            $subQuery->andWhere(['<=', 'b.date', Yii::$app->getFormatter()->asDate($this->toDate, 'yyyy-MM-dd')]);
            $masterSubQuery->andWhere(['<=', 'p.date', Yii::$app->getFormatter()->asDate($this->toDate, 'yyyy-MM-dd')]);
        }

        $query = (new Query())
                ->select(['*'])
                ->from(['b' => $masterSubQuery])
                ->where(['<', 'saldo', 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }

    public function searchByName($name) {
        /** @var Query $query */
        $query = Customer::find();
        $query->orWhere(['like', 'name', $name])
                ->orWhere(['like', 'lastname', $name])
                ->orderBy(['lastname' => SORT_ASC, 'name' => SORT_ASC]);

        return $query;
    }

}
