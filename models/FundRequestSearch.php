<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\FundRequests;

/**
 * FundRequestSearch represents the model behind the search form of `app\models\FundRequests`.
 */
class FundRequestSearch extends FundRequests
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fund_request_id', 'request_user_id', 'is_active', 'is_deleted'], 'integer'],
            [['fund_request_number', 'request_description', 'file', 'created_at', 'updated_at', 'amount'], 'safe'],
            [['request_amount'], 'number'],
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
        $query = FundRequests::find();

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
            'fund_request_id' => $this->fund_request_id,
            'request_user_id' => $this->request_user_id,
            'request_amount' => $this->request_amount,
            'is_active' => $this->is_active,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'amount' => $this->amount,
        ]);

        $query->andFilterWhere(['like', 'fund_request_number', $this->fund_request_number])
            ->andFilterWhere(['like', 'request_description', $this->request_description])
            ->andFilterWhere(['like', 'file', $this->file]);

        return $dataProvider;
    }
}
