<?php

namespace app\modules\api\controllers;

use app\models\example\Category;
use Yii;

class DashboardController extends BaseApiController
{
    public function actionSummary()
    {
        try {
            $totalCategories = Category::find()->count();
            $activeCategories = Category::find()->where(['is_active' => true])->count();

            return $this->success([
                'total_categories' => (int)$totalCategories,
                'active_categories' => (int)$activeCategories,
                'inactive_categories' => (int)($totalCategories - $activeCategories),
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function actionStats()
    {
        try {
            $monthlyStats = [];
            for ($i = 5; $i >= 0; $i--) {
                $startDate = date('Y-m-01', strtotime("-$i months"));
                $endDate = date('Y-m-t', strtotime("-$i months"));
                $monthName = date('M', strtotime("-$i months"));

                $count = Category::find()
                    ->where(['>=', 'created_at', $startDate])
                    ->andWhere(['<=', 'created_at', $endDate])
                    ->count();

                $monthlyStats[] = [
                    'month' => $monthName,
                    'count' => (int)$count,
                ];
            }

            return $this->success($monthlyStats);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
