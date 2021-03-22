<?php

namespace app\modules\westnet\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\Vendor;

/**
 * UserVendorSearch represents the model behind the search form about `app\modules\westnet\models\Vendor`.
 */
class VendorSearch extends Vendor
{
    public $status;
    public $superadmin;
    public $created_at;
    public $updated_at;
    public $email;
    public $email_confirmed;
    public $username;

    public function rules()
    {
        return [
            [['status', 'superadmin', 'created_at', 'updated_at', 'email_confirmed'], 'integer'],
            [['username', 'email', 'name', 'lastname'], 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Vendor::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if($this->username){
            $query->leftJoin('user', 'user.id = vendor.user_id')
                ->andWhere(['like','user.username', $this->username]);
        }

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'lastname', $this->lastname]);

        return $dataProvider;
    }
}
