<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MonthlyInvoice;

/**
 * MonthlyInvoiceSearch represents the model behind the search form of `app\models\MonthlyInvoice`.
 */
class MonthlyInvoiceSearch extends MonthlyInvoice
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['monthly_invoice_id', 'receiver_id', 'is_paid', 'is_deleted'], 'integer'],
            [['monthly_invoice_number', 'instalment_month', 'instalment_year', 'created_at', 'updated_at'], 'safe'],
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
        $query = MonthlyInvoice::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['monthly_invoice_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'monthly_invoice_id' => $this->monthly_invoice_id,
            'receiver_id' => $this->receiver_id,
            'amount' => $this->amount,
            'is_paid' => $this->is_paid,
            'is_deleted' => 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'monthly_invoice_number', $this->monthly_invoice_number])
            ->andFilterWhere(['like', 'instalment_month', $this->instalment_month])
            ->andFilterWhere(['like', 'instalment_year', $this->instalment_year]);

        return $dataProvider;
    }
}
