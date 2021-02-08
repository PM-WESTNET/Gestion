<?php

namespace app\modules\media\models\types\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\media\models\types\Image;

/**
 * ImageSearch represents the model behind the search form about `app\modules\media\models\types\Image`.
 */
class ImageSearch extends Image
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['media_id', 'width', 'height', 'create_timestamp'], 'integer'],
            [['title', 'description', 'name', 'base_url', 'relative_url', 'type', 'mime', 'extension', 'create_date', 'create_time', 'status'], 'safe'],
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
        $query = Image::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'media_id' => $this->media_id,
            'size' => $this->size,
            'width' => $this->width,
            'height' => $this->height,
            'create_date' => $this->create_date,
            'create_time' => $this->create_time,
            'create_timestamp' => $this->create_timestamp,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'base_url', $this->base_url])
            ->andFilterWhere(['like', 'relative_url', $this->relative_url])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'mime', $this->mime])
            ->andFilterWhere(['like', 'extension', $this->extension])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
