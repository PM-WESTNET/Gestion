<?php

namespace app\modules\media\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\media\models\Media;

/**
 * ImageSearch represents the model behind the search form about `app\modules\media\models\types\Image`.
 */
class MediaSearch extends Media
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['media_id', 'width', 'height'], 'integer'],
            [['title', 'description', 'name', 'base_url', 'relative_url', 'class', 'mime', 'extension', 'create_date', 'create_time', 'status'], 'safe'],
            [['size'], 'number'],
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
        $query = Media::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'create_timestamp' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'media_id' => $this->media_id,
            'size' => $this->size,
            'width' => $this->width,
            'height' => $this->height,
            'create_date' => $this->create_date,
        ]);
        
        $query->andFilterWhere(['class' => $this->class])
            ->andFilterWhere(['status' => $this->status]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'mime', $this->mime])
            ->andFilterWhere(['like', 'extension', $this->extension]);
            
        return $dataProvider;
    }
}
