<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PaymentRelease;

/**
 * PaymentReleaseSearch represents the model behind the search form of `app\models\PaymentRelease`.
 */
class PaymentReleaseSearch extends PaymentRelease
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_release_id', 'fund_request_id', 'release_by', 'is_deleted'], 'integer'],
            [['release_invoice_number', 'note', 'created_at', 'updated_at'], 'safe'],
            [['amount'], 'number'],
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
        $query = PaymentRelease::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['payment_release_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'payment_release_id' => $this->payment_release_id,
            'fund_request_id' => $this->fund_request_id,
            'release_by' => $this->release_by,
            'amount' => $this->amount,
            'is_deleted' => 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'release_invoice_number', $this->release_invoice_number])
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
