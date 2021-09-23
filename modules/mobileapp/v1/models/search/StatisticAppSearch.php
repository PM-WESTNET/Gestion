<?php

namespace app\modules\mobileapp\v1\models\search;

use app\modules\config\models\Config;
use app\modules\mobileapp\v1\models\StatisticApp;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * UserAppActivitySearch represents the model behind the search form of `app\modules\mobileapp\v1\models\UserAppActivity`.
 */
class StatisticAppSearch extends StatisticApp
{

    public function rules()
    {
        return [
            [['type', 'description', 'created_at', 'customer_code', 'total'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'type' => Yii::t('app', 'Tipo'),
            'description' => Yii::t('app', 'Descripción'),
            'created_at' => Yii::t('app', 'Fecha de Creación'),
            'customer_code' => Yii::t('app', 'Código de Cliente'),
        ]);
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
        $this->load($params);

        $query = StatisticApp::find()
        ->select(['COUNT(sa.statistic_app_id) as total', 'sa.type', 'sa.description'])
        ->from('statistic_app sa')
        ->groupBy('type');
    

        /*if($this->mobile_push_id){
            $query->andFilterWhere(['mphua.mobile_push_id' => $this->mobile_push_id]);
        }

        if($this->customer_id){
            $query->andFilterWhere(['c.customer_id' => $this->customer_id]);
        }

        if($this->customer_code){
            $query->andFilterWhere(['c.code' => $this->customer_code]);
        }*/

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        return $dataProvider;
    }
}
