<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\ProfileClass;

/**
 * ProfileClassSearch represents the model behind the search form about `app\modules\sale\models\ProfileClass`.
 */
class ProfileClassSearch extends ProfileClass
{
    public function rules()
    {
        return [
            [['profile_class_id'], 'integer'],
            [['name', 'data_type'], 'safe'],
            [['status'],'in','range'=>['enabled','disabled']],
            [['multiple'],'boolean'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = ProfileClass::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'data_type', $this->data_type])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
