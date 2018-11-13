<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\SearchBuilders\ProductSearchBuilder;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductsController extends Controller
{
//    public function index(Request $request)
//    {
//        $builder = Product::query()->where('on_sale', true);
//        if ($search = $request->input('search', '')) {
//            $like = '%'.$search.'%';
//            $builder->where(function ($query) use ($like) {
//                $query->where('title', 'like', $like)
//                    ->orWhere('description', 'like', $like)
//                    ->orWhereHas('skus', function ($query) use ($like) {
//                        $query->where('title', $like)
//                            ->orWhere('description', $like);
//                    });
//            });
//        }
//
//        if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
//            if ($category->is_directory) {
//                $builder->whereHas('category', function ($query) use ($category) {
//                    $query->where('path', 'like', $category->path.$category->id.'-%');
//                });
//            } else {
//                $builder->where('category_id', $category->id);
//            }
//        }
//
//        if ($order = $request->input('order', '')) {
//            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
//                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
//                    $builder->orderBy($m[1], $m[2]);
//                }
//            }
//        }
//
//        $products = $builder->paginate(16);
//        return view('products.index', [
//            'products' => $products,
//            'filters' => [
//                'search' => $search,
//                'order' => $order,
//            ],
//            'category' => $category ?? null,
////            'categoryTree' => $categoryService->getCategoryTree(),
//        ]);
//    }

//    public function index(Request $request)
//    {
//        $page = $request->input('page', 1);
//        $perPage = 16;
//
//        $params = [
//            'index' => 'products',
//            'type' => '_doc',
//            'body' => [
//                'from' => ($page - 1) * $perPage,
//                'size' => $perPage,
//                'query' => [
//                    'bool' => [
//                        'filter' => [
//                            ['term' => ['on_sale' => true]],
//                        ],
//                    ],
//                ],
//            ],
//        ];
//
//
//        //排序
//        if ($order = $request->input('order', '')) {
//            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
//                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
//                    $params['body']['sort'] = [[$m[1]=>$m[2]]];
//                }
//            }
//        }
//
//        //面包屑分类导航
//        if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
//            if ($category->is_directory) {
//                $params['body']['query']['bool']['filter'][] = [
//                    'prefix' => ['category_path' => $category->path.$category->id.'-'],
//                ];
//            } else {
//                $params['body']['query']['bool']['filter'][] = ['term' => ['category_id' => $category->id]];
//            }
//        }
//
//        //多字段搜索
//        if ($search = $request->input('search', '')) {
//            $keywords = array_filter(explode(' ', $search));
//
//            $params['body']['query']['bool']['must'] = [];
//            foreach ($keywords as $keyword) {
//                $params['body']['query']['bool']['must'][] = [
//                    'multi_match' => [
//                        'query' => $keyword,
//                        'fields' => [
//                            'title^3',
//                            'long_title^2',
//                            'category^2', // 类目名称
//                            'description',
//                            'skus_title',
//                            'skus_description',
//                            'properties_value',
//                        ],
//                    ],
//                ];
//            }
//
//        }
//
//        //分面搜索
//        if ($search || isset($category)) {
//            $params['body']['aggs'] = [
//                'properties' => [
//                    'nested' => [
//                        'path' => 'properties',
//                    ],
//                    'aggs' => [
//                        'properties' => [
//                            'terms' => [
//                                'field' => 'properties.name',
//                            ],
//                            'aggs' => [
//                                'value' => [
//                                    'terms' => [
//                                        'field' => 'properties.value',
//                                    ],
//                                ],
//                            ]
//                        ],
//                    ],
//                ],
//            ];
//        }
//
//        $propertyFilters = [];
//        if ($filterString = $request->input('filters')) {
//            $filterArray = explode('|', $filterString);
//
//
//
//            foreach ($filterArray as $filter) {
//                list($name, $value) = explode(':', $filter);
//
//                // 将用户筛选的属性添加到数组中
//                $propertyFilters[$name] = $value;
//
//                $params['body']['query']['bool']['filter'][] = [
//                  'nested' => [
//                      'path' => 'properties',
//                      'query' => [
//                          ['term' => ['properties.search_name' => $filter]],
////                          ['term' => ['properties.name' => $name]],
////                          ['term' => ['properties.value' => $value]],
//                      ]
//                  ]
//                ];
//            }
//        }
//
//
//        $result = app('es')->search($params);
//
//        $productIds = collect($result['hits']['hits'])->pluck('_id')->all();
//
//        $products = Product::query()
//            ->whereIn('id', $productIds)
//            ->orderByRaw(sprintf("FIND_IN_SET(id, '%s')", join(',', $productIds)))
//            ->get();
//
//        $pager = new LengthAwarePaginator($products, $result['hits']['total'], $perPage, $page, [
//           'path' => route('products.index', false),
//        ]);
//
//        $properties = [];
//
//        if (isset($result['aggregations'])) {
//            $properties = collect($result['aggregations']['properties']['properties']['buckets'])
//                ->map(function ($bucket) {
//                    return [
//                        'key' => $bucket['key'],
//                        'values' => collect($bucket['value']['buckets'])->pluck('key')->all(),
//                    ];
//                })
//                ->filter(function ($property) use ($propertyFilters) {
//                    return count($property['values']) > 1 && !isset($propertyFilters[$property['key']]) ;
//                });
//
//
//        }
//
//        dd($result);
//        return view('products.index', [
//            'products' => $pager,
//            'filters' => [
//                'search' => $search,
//                'order' => $order,
//            ],
//            'category' => $category ?? null,
//            'properties' => $properties,
//            'propertyFilters' => $propertyFilters,
//        ]);
//    }

    public function index(Request $request)
    {
        $page    = $request->input('page', 1);
        $perPage = 16;
        // 新建查询构造器对象，设置只搜索上架商品，设置分页
        $builder = (new ProductSearchBuilder())->onSale()->paginate($perPage, $page);

        if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
            // 调用查询构造器的类目筛选
            $builder->category($category);
        }

        if ($search = $request->input('search', '')) {
            $keywords = array_filter(explode(' ', $search));
            // 调用查询构造器的关键词筛选
            $builder->keywords($keywords);
        }

        if ($search || isset($category)) {
            // 调用查询构造器的分面搜索
            $builder->aggregateProperties();
        }

        $propertyFilters = [];
        if ($filterString = $request->input('filters')) {
            $filterArray = explode('|', $filterString);
            foreach ($filterArray as $filter) {
                list($name, $value) = explode(':', $filter);
                $propertyFilters[$name] = $value;
                // 调用查询构造器的属性筛选
                $builder->propertyFilter($name, $value);
            }
        }

        if ($order = $request->input('order', '')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // 调用查询构造器的排序
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        $result = app('es')->search($builder->getParams());

        $productIds = collect($result['hits']['hits'])->pluck('_id')->all();

        $products = Product::query()
            ->byIds($productIds)
            ->get();

        $pager = new LengthAwarePaginator($products, $result['hits']['total'], $perPage, $page, [
           'path' => route('products.index', false),
        ]);

        $properties = [];

        if (isset($result['aggregations'])) {
            $properties = collect($result['aggregations']['properties']['properties']['buckets'])
                ->map(function ($bucket) {
                    return [
                        'key' => $bucket['key'],
                        'values' => collect($bucket['value']['buckets'])->pluck('key')->all(),
                    ];
                })
                ->filter(function ($property) use ($propertyFilters) {
                    return count($property['values']) > 1 && !isset($propertyFilters[$property['key']]) ;
                });


        }

        return view('products.index', [
            'products' => $pager,
            'filters' => [
                'search' => $search,
                'order' => $order,
            ],
            'category' => $category ?? null,
            'properties' => $properties,
            'propertyFilters' => $propertyFilters,
        ]);
    }

    public function show(Product $product, Request $request, ProductService $service)
    {
        if (!$product->on_sale) {
            return new InvalidRequestException('商品未上架');
        }

        $favored= false;

        if ($user = $request->user()) {
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

//        //取4个上架的商品
//        $builder = (new ProductSearchBuilder())->onSale()->paginate(4, 1);
//
//        foreach ($product->properties as $property) {
//            $builder->propertyFilter($property->name, $property->value, 'should');
//        }
//
//        $builder->minShouldMatch(ceil(count($product->properties)/2));
//
//        $params = $builder->getParams();
//
//        $params['body']['query']['bool']['must_not'] = [['term' => ['_id' => $product->id]]];
//
//        $result = app('es')->search($params);
//
//        $similarProductIds = collect($result['hits']['hits'])->pluck('_id')->all();

        $similarProductIds = $service->getSimilarProductIds($product, 4);

        $similarProductIds = Product::query()
            ->byIds($similarProductIds)
            ->get();

        $reviews = OrderItem::query()
            ->with(['order.user', 'productSku']) // 预先加载关联关系
            ->where('product_id', $product->id)
            ->whereNotNull('reviewed_at') // 筛选出已评价的
            ->orderBy('reviewed_at', 'desc') // 按评价时间倒序
            ->limit(10) // 取出 10 条
            ->get();


        return view('products.show', [
            'product' => $product,
            'favored' => $favored,
            'reviews' => $reviews,
            'similar' => $similarProductIds,
            ]);
    }

    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);
        return [];
    }

    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);

        return [];
    }

    public function favorites(Request $request)
    {
        $user = $request->user();
        $products = $user->favoriteProducts()->paginate(16);
        return view('products.favorites', ['products' => $products]);
    }
}
