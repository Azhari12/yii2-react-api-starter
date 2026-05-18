<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\rbac\components\Helper;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\rbac\models\searchs\User */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('rbac-admin', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'pgw_nomor',
                'label' => 'ID',
                'headerOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'pgw_nama',
                'label' => 'Nama',
                'headerOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'pgw_username',
                'label' => 'Username',
                'headerOptions' => ['style' => 'text-align: center;'],
            ],
            [
                'attribute' => 'pgw_aktif',
                'label' => 'Status',
                'format' => 'html',
                'headerOptions' => ['style' => 'width: 10%;text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'value' => function($model) {
                    return $model->pgw_aktif == 0 ? '<span class="badge badge-danger badge-pill">Non-Aktif</span>' : '<span class="badge badge-success badge-pill">Aktif</span>';
                },
                'filter' => [
                    0 => 'Non-Aktif',
                    1 => 'Aktif'
                ]
            ],
            // [
                // 'class' => 'yii\grid\ActionColumn',
                // 'template' => Helper::filterActionColumn(['view']),
                // 'template' => Helper::filterActionColumn(['view', 'activate', 'delete']),
                // 'buttons' => [
                //     'activate' => function($url, $model) {
                //         // if ($model->status == 10) {
                //         //     return '';
                //         // }
                //         $options = [
                //             'title' => Yii::t('rbac-admin', 'Activate'),
                //             'aria-label' => Yii::t('rbac-admin', 'Activate'),
                //             'data-confirm' => Yii::t('rbac-admin', 'Are you sure you want to activate this user?'),
                //             'data-method' => 'post',
                //             'data-pjax' => '0',
                //         ];
                //         return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, $options);
                //     }
                //     ]
            // ],
        ],
        'pager' => [
            'class' => 'app\components\GridPager',
        ],
    ]);
        ?>
</div>
