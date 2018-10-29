<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use App\Models\Installment;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;

class InstallmentsController extends Controller
{
    public function index(Request $request)
    {
        $installments = Installment::query()
            ->where('user_id', $request->user()->id)
            ->paginate(10);

        return view('installments.index', ['installments' => $installments]);
    }

    public function show(Installment $installment)
    {
        $this->authorize('own', $installment);
        $items = $installment->items()->orderBy('sequence')->get();

        return view('installments.show', [
           'installment' => $installment,
           'items' => $items,
           'nextItem' => $items->where('paid_at', null)->first(),
        ]);
    }

    public function payByAlipay(Installment $installment)
    {
        if ($installment->order->closed) {
            throw new InvalidRequestException('对应的商品订单已被关闭');
        }

        if ($installment->status === Installment::STATUS_FINISH) {
            throw new InvalidRequestException('该分期订单已结清');
        }

        if (!$nextItem = $installment->items()->whereNull('paid_at')->orderBy('sequence')->first()) {
            throw new InvalidRequestException('该分期订单已结清');
        }

        return app('alipay')->web([
           'out_trade_no' => $installment->no.'_'.$nextItem->sequence,
            'total_amount' => $nextItem->total,
            'subject' => '支付 Laravel Shop 的分期订单'.$installment->no,
            'notify_url' => ngrok_url('installments.alipay.notify'),
            'return_url' => route('installments.alipay.return'),
        ]);
    }

    public function payByWechat(Installment $installment)
    {
        if ($installment->order->closed) {
            throw new InvalidRequestException('对应的商品订单已被关闭');
        }

        if ($installment->status === Installment::STATUS_FINISH) {
            throw new InvalidRequestException('该分期订单已结清');
        }

        if (!$nextItem = $installment->items()->whereNull('paid_at')->orderBy('sequence')->first()) {
            throw new InvalidRequestException('该分期订单已结清');
        }

        $wechatOrder = app('wechat_pay')->sacn([
            'out_trade_no' => $installment->no.'_'.$nextItem->sequence,
            'total_fee'    => $nextItem->total * 100,
            'body'         => '支付 Laravel Shop 的分期订单：'.$installment->no,
            'notify_url'   => ngrok_url('installments.wechat.notify'),
        ]);

        $qrCode = new QrCode($wechatOrder->code_url);

        return response($qrCode->writeString(), 200, ['Content-Type' => $qrCode->getContentType()]);
    }

    public function alipayReturn()
    {
        try {
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }

    public function alipayNotify()
    {
        //校验回调参数
        $data = app('alipay')->verify();

        if ($this->paid($data->out_trade_no, 'alipay', $data->trade_no)) {
            return app('alipay')->success();
        }
        return 'fail';
    }

    public function wechatNotify()
    {
        $data = app('wechat_pay')->verify();
        if ($this->paid($data->out_trade_no, 'wechat', $data->transaction_id)) {
            return app('wechat_pay')->success();
        }

        return 'fail';
    }

    protected function paid($out_trade_no, $paymentMethod, $paymentNo)
    {
        //批量赋值
        list($no, $sequence) = explode('_', $out_trade_no);

        if (!$installment = Installment::query()->where('no', $no)->first()) {
            return 'fail';
        }

        if (!$item = $installment->items()->where('sequence', $sequence)->first()) {
            return 'fail';
        }

        if ($item->paid_at) {
            return app('alipay')->success();
        }

        // 更新对应的还款计划
        $item->update([
            'paid_at'        => Carbon::now(), // 支付时间
            'payment_method' => $paymentMethod, // 支付方式
            'payment_no'     => $paymentNo, // 支付宝订单号
        ]);

        if ($item->sequence === 0) {
            $installment->update(['status' => Installment::STATUS_REPAYING]);

            $installment->order->update([
                'paid_at' => Carbon::now(),
                'payment_method' => 'installment',
                'payment_no' => $no,
            ]);

            event(new OrderPaid(($installment->order)));
        }

        if ($item->sequence === $installment->count - 1) {
            $installment->update(['status' => Installment::STATUS_FINISH]);
        }

        return true;
    }
}
