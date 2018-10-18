<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Models\Product;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;

class ProductsController extends CommonProductController
{
    public function getProductType()
    {
        // TODO: Implement getProductType() method.
        return Product::TYPE_NORMAL;
    }

    /**
     * Make a grid builder.
     * @param Grid $grid
     */
    protected function customGrid(Grid $grid)
    {
        // 使用 with 来预加载商品类目数据，减少 SQL 查询
        $grid->model()->with(['category']);
        $grid->id('ID')->sortable();
        $grid->title('商品名称');
        // Laravel-Admin 支持用符号 . 来展示关联关系的字段
        $grid->column('category.name', '类目');
        $grid->on_sale('已上架')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->price('价格');
        $grid->rating('评分');
        $grid->sold_count('销量');
        $grid->review_count('评论数');
    }

    /**
     * Make a form builder.
     *
     * @param  Form $form
     */
    protected function customForm(Form $form)
    {
    }
}
