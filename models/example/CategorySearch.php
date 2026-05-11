<?php

namespace app\models\example;

use yii\data\ActiveDataProvider;

class CategorySearch extends Category
{
    public function rules()
    {
        return [
            [['name', 'description'], 'safe'],
            [['is_active'], 'boolean'],
        ];
    }

    public function search($params)
    {
        $query = Category::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $params['page_size'] ?? 10,
                'page' => isset($params['page']) ? $params['page'] - 1 : 0,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        if (isset($params['name']) && !empty($params['name'])) {
            $query->andFilterWhere(['ilike', 'name', $params['name']]);
        }

        if (isset($params['is_active'])) {
            $query->andFilterWhere(['is_active' => $params['is_active']]);
        }

        return $dataProvider;
    }
}
