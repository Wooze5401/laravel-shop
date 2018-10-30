<?php

namespace App\Console\Commands\Cron;

use App\Models\Installment;
use App\Models\InstallmentItem;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateInstallmentFine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:calculate-installment-fine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算分期付款逾期费';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        InstallmentItem::query()
            ->with('installment')
            ->whereHas('installment', function ($query) {
                $query->where('status', Installment::STATUS_REPAYING);
            })->where('due_date', '<=', Carbon::now())
            ->whereNull('paid_at')
            //每次取出1000条id，因为whereIn接收的参数有数量限制，等回调处理完毕后再取1000条，按照ID升序

            //精度计算原则：优先执行让结果绝对值变大的数
            ->chunkById(1000, function ($items) {
                foreach ($items as $item) {
                    //diffInDays获取天数
                    $overdueDays = Carbon::now()->diffInDays($item->due_date);

                    //本金加手续费
                    $base = big_number($item->base)->add($item->fee)->getValue();

                    //逾期费用
                    $fine = big_number($base)
                        ->multiply($overdueDays)
                        ->multiply($item->installment->fine_rate)
                        ->divide(100)
                        ->getValue();

                    $fine = big_number($fine)->compareTo($base) === 1 ? $base : $fine;
                    $item->update([
                        'fine' => $fine,
                    ]);
                }
            });
    }
}
