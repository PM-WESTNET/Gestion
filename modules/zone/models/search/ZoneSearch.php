<?php

namespace app\modules\zone\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\zone\models\Zone;

/**
 * ZoneSearch represents the model behind the search form about `app\modules\zone\models\Zone`.
 */
class ZoneSearch extends Zone
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['zone_id', 'parent_id', 'create_timestamp', 'update_timestamp', 'postal_code'], 'integer'],
            [['name', 'status', 'type'], 'safe'],
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
        $query = Zone::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'zone_id' => $this->zone_id,
            'parent_id' => $this->parent_id,
            'status' => $this->status,
            'type' => $this->type,
            'postal_code'=> $this->postal_code,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->orderBy(['lft'=>SORT_ASC]);

        return $dataProvider;
    }
    
    public function isFiltered(){
        if(empty($this->name) && empty($this->zone_id) && empty($this->status) && empty($this->parent_id) && empty($this->type)&& empty($this->postal_code))
            return false;
        else
            return true;
    }
}
