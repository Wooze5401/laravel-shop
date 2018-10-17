<?php
namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function getCategoryTree($parentId = null, $allCategories = null)
    {
//        一次取出所有分类
        if (is_null($allCategories)) {
            $allCategories = Category::all();
        }

        return $allCategories
            ->where('parent_id', $parentId)
            ->map(function (Category $category) use ($allCategories) {
                $data = ['id' => $category->id, 'name' => $category->name];

//              没有子目录则返回
                if (!$category->is_directory) {
                    return $data;
                }

                $data['children'] = $this->getCategoryTree($category->id, $allCategories);
                return $data;
            });
    }
}