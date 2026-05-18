<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel mdm\admin\models\searchs\Assignment */
/* @var $usernameField string */
/* @var $extraColumns string[] */

$this->title = Yii::t('rbac-admin', 'Assignments');
$this->params['breadcrumbs'][] = $this->title;

$columns = [
  ['class' => 'yii\grid\SerialColumn'],
  [
    'attribute' => 'userid',
    'label' => 'ID USER',
    'headerOptions' => ['style' => 'text-align: center;'],
  ],
  [
    'attribute' => 'id_pegawai',
    'label' => 'ID PEGAWAI',
    'headerOptions' => ['style' => 'text-align: center;'],
  ],
  [
    'attribute' => 'username',
    'label' => 'NIK/NIP',
    'headerOptions' => ['style' => 'text-align: center;'],
  ],
  [
    'attribute' => 'nama',
    'label' => 'NAMA',
    'headerOptions' => ['style' => 'text-align: center;'],
  ],
  [
    'attribute' => 'role',
    'label' => 'ROLE',
    'headerOptions' => ['style' => 'text-align: center;'],
  ],
];
if (!empty($extraColumns)) {
  $columns = array_merge($columns, $extraColumns);
}
$columns[] = [
  'class' => 'yii\grid\ActionColumn',
  'template' => '{view}'
];
?>
<div class="assignment-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <?php Pjax::begin(); ?>
  <?=
  GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $columns,
  ]);
  ?>
  <?php Pjax::end(); ?>

</div>