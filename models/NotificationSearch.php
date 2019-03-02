<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Notifications;

/**
 * NotificationSearch represents the model behind the search form of `app\models\Notifications`.
 */
class NotificationSearch extends Notifications
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notification_id', 'type_id', 'is_deleted'], 'integer'],
            [['type', 'comments', 'created_at'], 'safe'],
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
        $query = Notifications::find();

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
            'notification_id' => $this->notification_id,
            'type_id' => $this->type_id,
            'created_at' => $this->created_at,
            'is_deleted' => $this->is_deleted,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'comments', $this->comments]);

        return $dataProvider;
    }
}
