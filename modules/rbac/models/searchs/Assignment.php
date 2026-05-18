<?php

namespace app\modules\rbac\models\searchs;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AssignmentSearch represents the model behind the search form about Assignment.
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Assignment extends Model
{
  public $userid;
  public $id_pegawai;
  public $username;
  public $nama;
  public $role;

  /**
   * @inheritdoc
   */
  public function rules()
  {
    return [
      [['id_pegawai', 'userid'], 'integer'],
      [['username', 'nama', 'role'], 'safe'],
    ];
  }

  /**
   * Creates data provider instance with search query applied
   *
   * @param array $params
   *
   * @return ActiveDataProvider
   */
  public function search($params, $class, $usernameField)
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

    if (!($this->load($params) && $this->validate())) {
      return $dataProvider;
    }

    $query->andFilterWhere(['ilike', 'username', $this->username])
      ->andFilterWhere(['ilike', 'role', $this->role])
      ->andFilterWhere(['=', 'userid', $this->userid])
      ->andFilterWhere(['=', 'id_pegawai', $this->id_pegawai])
      ->andFilterWhere(['ilike', 'nama', $this->nama]);

    return $dataProvider;
  }
}
