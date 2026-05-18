<?php

namespace app\modules\rbac\models\searchs;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * User represents the model behind the search form about `app\modules\rbac\models\User`.
 */
class User extends Model
{
  public $pgw_id;
  public $pgw_username;
  public $pgw_nama;
  public $pgw_nomor;
  public $pgw_aktif;

  /**
   * @inheritdoc
   */
  public function rules()
  {
    return [
      [['pgw_id', 'pgw_aktif'], 'integer'],
      [['pgw_username', 'pgw_nama', 'pgw_nomor'], 'safe'],
    ];
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
    /* @var $query \yii\db\ActiveQuery */
    // $class = Yii::$app->getUser()->identityClass ? : 'app\modules\rbac\models\User';
    $class = Yii::$app->getUser()->identityClass ?: 'app\models\auth\User';
    $query = $class::find();

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'pagination' => [
        'pageSize' => Yii::$app->params['setting']['paging']['size']['long']
      ]
    ]);

    $this->load($params);
    if (!$this->validate()) {
      $query->where('1=0');
      return $dataProvider;
    }

    $query->andFilterWhere([
      'pgw_aktif' => $this->pgw_aktif,
    ]);

    $query->andFilterWhere(['ilike', 'pgw_username', $this->pgw_username])
      ->andFilterWhere(['ilike', 'pgw_nomor', $this->pgw_nomor])
      ->andFilterWhere(['ilike', 'pgw_nama', $this->pgw_nama]);

    return $dataProvider;
  }
}
