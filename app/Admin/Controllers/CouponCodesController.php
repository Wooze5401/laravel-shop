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

    public function create(Content $content)
    {
        return $content
            ->header('新增优惠券')
            ->body($this->form());
    }

    public function edit(Content $content, $id)
    {
        return $content
            ->header('编辑优惠券')
            ->body($this->form()->edit($id));
    }


    protected function form()
    {
        $form = new Form(new Couponcode);

        $form->display('id', 'ID');
        $form->text('name', '名称')->rules('required');
        $form->text('code', '优惠码')->rules(function ($form) {
            if ($id = $form->model()->id) {
                return 'nullable|unique:coupon_codes,code,'.$id.',id';
            } else {
                return 'nullable|unique:coupon_codes';
            }
        });
        $form->radio('type', '类型')->options(CouponCode::$typeMap)->rules('required');
        $form->text('value', '折扣')->rules(function ($form) {
            if ($form->type === CouponCode::TYPE_PERCENT) {
                return 'required|numeric|between:1,99';
            } else {
                return 'required|numeric|min:0.01';
            }
        });
        $form->number('total', '总量')->rules('required|numeric|min:0');
        $form->decimal('min_amount', '最底金额')->rules('required|numeric');
        $form->datetime('not_before', '开始时间')->default(date('Y-m-d H:i:s'));
        $form->datetime('not_after', '结束时间')->default(date('Y-m-d H:i:s'));
        $form->radio('enabled', '启用')->options(['1' => '是', '0' => '否']);
        $form->saving(function (Form $form) {
            if (!$form->code) {
                $form->code = CouponCode::findAvailableCode();
            }
        });
        return $form;
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
