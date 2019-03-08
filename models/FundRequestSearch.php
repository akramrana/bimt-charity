<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\FundRequests;

/**
 * FundRequestSearch represents the model behind the search form of `app\models\FundRequests`.
 */
class FundRequestSearch extends FundRequests {

    public $status_id;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['fund_request_id', 'request_user_id', 'is_active', 'is_deleted'], 'integer'],
            [['fund_request_number', 'request_description', 'file', 'created_at', 'updated_at', 'request_amount', 'status_id'], 'safe'],
            [['request_amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios() {
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
    public function search($params) {
        $query = FundRequests::find();
        $query->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM fund_request_status AS t1
                                        LEFT OUTER JOIN fund_request_status AS t2 ON t1.fund_request_id = t2.fund_request_id 
                                                AND (t1.created_at < t2.created_at 
                                                 OR (t1.created_at = t2.created_at AND t1.fund_request_status_id < t2.fund_request_status_id))
                                        WHERE t2.fund_request_id IS NULL
                                        ) as temp', 'temp.fund_request_id = fund_requests.fund_request_id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['fund_request_id' => SORT_DESC]],
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
            'is_deleted' => 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'temp.status_id' => $this->status_id,
        ]);

        $query->andFilterWhere(['like', 'fund_request_number', $this->fund_request_number])
                ->andFilterWhere(['like', 'request_description', $this->request_description])
                ->andFilterWhere(['like', 'file', $this->file]);

        return $dataProvider;
    }

}
