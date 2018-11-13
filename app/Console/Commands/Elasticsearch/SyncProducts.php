<?php

namespace App\Console\Commands\Elasticsearch;

use App\Models\Product;
use Illuminate\Console\Command;

class SyncProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:sync-products {--index=products}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步数据到Elasticsearch';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $es = app('es');

        Product::query()
            ->with(['skus', 'properties'])
            ->chunkById(100, function ($products) use ($es) {
                $this->info(sprintf('正在同步 ID 范围为 %s 至 %s 的商品', $products->first()->id, $products->last()->id));

                $req = ['body' => []];

                foreach ($products as $product) {
                    $data = $product->toESArray();

                    //第一个参数为es操作
                    $req['body'][] = [
                        'index' => [
                            '_index' => $this->option('index'),
                            '_type' => '_doc',
                            '_id' => $data['id'],
                        ]
                    ];
                    //第二行为数据行
                    $req['body'][] = $data;
                }
                try {
                    //批量创建
                    //用一次API完成一批操作，提高性能
                    $es->bulk($req);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            });
        $this->info('同步完成');
    }
}

