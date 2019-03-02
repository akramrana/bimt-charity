<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Users;

/**
 * UserSearch represents the model behind the search form of `app\models\Users`.
 */
class UserSearch extends Users
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'enable_login', 'password', 'is_active', 'is_deleted'], 'integer'],
            [['fullname', 'image', 'email', 'phone', 'alt_phone', 'address', 'batch', 'department', 'user_type', 'created_at', 'updated_at'], 'safe'],
            [['recurring_amount'], 'number'],
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
        $query = Users::find();

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
            'user_id' => $this->user_id,
            'enable_login' => $this->enable_login,
            'password' => $this->password,
            'recurring_amount' => $this->recurring_amount,
            'is_active' => $this->is_active,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'alt_phone', $this->alt_phone])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'batch', $this->batch])
            ->andFilterWhere(['like', 'department', $this->department])
            ->andFilterWhere(['like', 'user_type', $this->user_type]);

        return $dataProvider;
    }
}
