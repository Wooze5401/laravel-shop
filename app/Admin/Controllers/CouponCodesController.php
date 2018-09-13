<?php

namespace App\Admin\Controllers;

use App\Models\Couponcode;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CouponCodesController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('优惠券列表')
            ->body($this->grid());
    }





    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Couponcode);

        $grid->model()->orderBy('created_at', 'desc');
        $grid->id('Id')->sortable();
        $grid->name('名称');
        $grid->code('优惠券');
        $grid->description('描述');
        $grid->column('usage', '用量')->display(function ($value) {
            return "{$this->used} / {$this->total}";
        });
//        $grid->type('类型')->display(function ($value) {
//            return CouponCode::$typeMap[$value];
//        });
//        $grid->value('折扣')->display(function ($value) {
//            return $this->type === CouponCode::TYPE_FIXED ? '￥'.$value : $value.'%';
//        });
//        $grid->total('总量');
//        $grid->used('已用');
//        $grid->min_amount('最底金额');
        $grid->enabled('是否启用')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->created_at('创建时间');

        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        return $grid;
    }
}
