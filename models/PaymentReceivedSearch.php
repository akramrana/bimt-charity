<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PaymentReceived;

/**
 * PaymentReceivedSearch represents the model behind the search form of `app\models\PaymentReceived`.
 */
class PaymentReceivedSearch extends PaymentReceived
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_received_id', 'donated_by', 'received_by', 'has_invoice', 'monthly_invoice_id', 'is_deleted'], 'integer'],
            [['received_invoice_number', 'comments', 'instalment_month', 'instalment_year'], 'safe'],
            [['amount', 'created_at', 'updated_at'], 'number'],
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
        $query = PaymentReceived::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['payment_received_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'payment_received_id' => $this->payment_received_id,
            'donated_by' => $this->donated_by,
            'received_by' => $this->received_by,
            'amount' => $this->amount,
            'has_invoice' => $this->has_invoice,
            'monthly_invoice_id' => $this->monthly_invoice_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_deleted' => 0,
        ]);

        $query->andFilterWhere(['like', 'received_invoice_number', $this->received_invoice_number])
            ->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'instalment_month', $this->instalment_month])
            ->andFilterWhere(['like', 'instalment_year', $this->instalment_year]);

        return $dataProvider;
    }
}
