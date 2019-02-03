<?php

namespace dench\cart\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use dench\cart\models\Order;

/**
 * OrderSearch represents the model behind the search form of `dench\cart\models\Order`.
 */
class OrderSearch extends Order
{
    public $buyer_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'buyer_id', 'amount', 'created_at', 'status'], 'integer'],
            [['text', 'phone', 'email', 'delivery', 'buyer_name'], 'safe'],
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
        $query = Order::find();

        $query->joinWith('buyer');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'buyer_id' => $this->buyer_id,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'order.phone', $this->phone])
            ->andFilterWhere(['like', 'order.email', $this->email])
            ->andFilterWhere(['like', 'order.delivery', $this->delivery])
            ->andFilterWhere(['like', 'buyer.name', $this->buyer_name]);

        return $dataProvider;
    }
}
