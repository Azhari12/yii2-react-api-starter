<?php

namespace app\modules\api\controllers;

use app\models\example\Category;
use app\models\example\CategorySearch;
use Yii;
use yii\helpers\ArrayHelper;

class CategoryController extends BaseApiController
{
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        $pagination = $dataProvider->getPagination();
        return [
            'success' => true,
            'page' => $pagination->getPage() + 1,
            'pageSize' => $pagination->getPageSize(),
            'totalCount' => $dataProvider->getTotalCount(),
            'totalPages' => $pagination->getPageCount(),
            'data' => ArrayHelper::toArray($dataProvider->getModels()),
        ];
    }

    public function actionView($id)
    {
        $model = Category::findOne($id);
        if (!$model) {
            return $this->error('Category not found.', 404);
        }
        return $this->success($model->toArray());
    }

    public function actionCreate()
    {
        $model = new Category();
        $data = Yii::$app->request->post();

        $model->name = $data['name'] ?? '';
        $model->description = $data['description'] ?? '';
        $model->is_active = $data['is_active'] ?? true;
        $model->created_at = date('Y-m-d H:i:s');

        if ($model->save()) {
            return $this->success($model->toArray(), 'Category created successfully.');
        }

        return $this->error('Failed to create category.', 400, $model->getErrors());
    }

    public function actionUpdate($id)
    {
        $model = Category::findOne($id);
        if (!$model) {
            return $this->error('Category not found.', 404);
        }

        $data = Yii::$app->request->post();
        $model->name = $data['name'] ?? $model->name;
        $model->description = $data['description'] ?? $model->description;
        $model->is_active = $data['is_active'] ?? $model->is_active;
        $model->updated_at = date('Y-m-d H:i:s');

        if ($model->save()) {
            return $this->success($model->toArray(), 'Category updated successfully.');
        }

        return $this->error('Failed to update category.', 400, $model->getErrors());
    }

    public function actionDelete($id)
    {
        $model = Category::findOne($id);
        if (!$model) {
            return $this->error('Category not found.', 404);
        }

        if ($model->delete()) {
            return $this->success(null, 'Category deleted successfully.');
        }

        return $this->error('Failed to delete category.', 400);
    }
}
