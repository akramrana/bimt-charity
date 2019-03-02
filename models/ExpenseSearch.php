<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Expenses;

/**
 * ExpenseSearch represents the model behind the search form of `app\models\Expenses`.
 */
class ExpenseSearch extends Expenses
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['expense_id', 'user_id', 'is_deleted'], 'integer'],
            [['purpose', 'created_at', 'updated_at'], 'safe'],
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
        $query = Expenses::find();

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
            'expense_id' => $this->expense_id,
            'user_id' => $this->user_id,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'purpose', $this->purpose]);

        return $dataProvider;
    }
}
