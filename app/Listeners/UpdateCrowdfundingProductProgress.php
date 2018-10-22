<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\Order;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class UpdateCrowdfundingProductProgress
{

    /**
     * Handle the event.
     *
     * @param  OrderPaid  $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        $order = $event->getOrder();

        if ($order->type !== Order::TYPE_CROWDFUNDING) {
            return;
        }

        $crowdfunding = $order->items[0]->product->crowdfunding;

        $data = Order::query()
//            找出众筹商品
            ->where('type', Order::TYPE_CROWDFUNDING)
//            已付款的
            ->orWhereNotNull('paid_at')
//            包含本商品的
            ->whereHas('items', function ($query) use ($crowdfunding) {
                $query->where('product_id', $crowdfunding->product_id);
            })->first([
//                总金额
                DB::raw('sum(total_amount) as total_amount'),
                DB::raw('count(distinct(user_id)) as user_count'),
            ]);

        $crowdfunding->update([
            'total_amount' => $data->total_amount,
            'user_count'   => $data->user_count,
        ]);
    }
}
