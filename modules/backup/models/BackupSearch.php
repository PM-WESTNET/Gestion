<?php

namespace app\modules\backup\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\backup\models\Backup;

/**
 * BackupSearch represents the model behind the search form of `app\modules\backup\models\Backup`.
 */
class BackupSearch extends Backup
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['backup_id', 'init_timestamp', 'finish_timestamp'], 'integer'],
            [['status', 'description'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Backup::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'backup_id' => $this->backup_id,
            'init_timestamp' => $this->init_timestamp,
            'finish_timestamp' => $this->finish_timestamp,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
