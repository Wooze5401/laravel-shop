@extends('layouts.app')
@section('title', '商品列表')

@section('content')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">

            <div class="card">
                <div class="card-body">


                    <div class="row">
                            <form action="{{route('products.index')}}" class="form-inline search-form">
                                {{--排序时的过滤条件--}}
                                <input type="hidden" name="filters">

                                {{--面包屑--}}
                                <a class="all-products" href="{{ route('products.index') }}">全部</a> &gt;
                                @if ($category)
                                    @foreach($category->ancestors as $ancestor)
                                        <span class="category">
                                            <a href="{{ route('products.index', ['category_id' => $ancestor->id]) }}">{{ $ancestor->name }}</a>
                                        </span>
                                        <span>&gt</span>
                                    @endforeach
                                        <span class="category">{{ $category->name }}</span><span>&gt</span>
                                        <!-- 当前类目的 ID，当用户调整排序方式时，可以保证 category_id 参数不丢失 -->
                                        <input type="hidden" name="category_id" value="{{ $category->id }}">
                                @endif

                                @foreach($propertyFilters as $name => $value)
                                    <span class="filter">{{ $name }}:
                                        <span class="filter-value">
                                            {{ $value }}
                                        </span>

                                        <a class="remove-filter" href="javascript: removeFilterFromQuery('{{ $name }}')">x</a>
                                    </span>
                                @endforeach

                                    <input type="text" class="form-control input-sm" name="search" placeholder="搜索">


                                    <button class="btn btn-primary btn-sm">搜索</button>


                                    <select name="order" class="form-control input-sm float-right">
                                        <option value="">排序方式</option>
                                        <option value="price_asc">价格从低到高</option>
                                        <option value="price_desc">价格从高到低</option>
                                        <option value="sold_count_desc">销量从高到低</option>
                                        <option value="sold_count_asc">销量从低到高</option>
                                        <option value="rating_desc">评价从高到低</option>
                                        <option value="rating_asc">评价从低到高</option>
                                    </select>
                            </form>

                    </div>

                    <div class="filters">
                        @if ($category && $category->is_directory)
                            <div class="row">
                                <div class="col-xs-3 filter-key">子类目：</div>
                                <div class="col-xs-9 filter-values">
                                    @foreach($category->children as $child)
                                        <a href="{{ route('products.index', ['category_id' => $child->id]) }}">{{ $child->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @foreach($properties as $property)
                            <div class="row">
                                <div class="col-xs-3 filter-key">{{ $property['key'] }}：</div>
                                <div class="col-xs-9 filter-values">
                                    @foreach($property['values'] as $value)
                                        <a href="javascript: appendFilterToQuery('{{ $property['key'] }}', '{{ $value }}')">{{ $value }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row products-list">
                        @foreach($products as $product)
                            <div class="col-xs-3 col-md-3 product-item">
                                <div class="product-content">
                                    <div class="top">
                                        <div class="img">
                                            <a href="{{ route('products.show', ['product' => $product->id]) }}">
                                                <img style="width: 100%; height: auto" src="{{$product->image_url}}" alt="">
                                            </a>
                                        </div>
                                        <div class="price"><b>￥</b>{{$product->price}}</div>
                                        <div class="title">
                                            <a href="{{ route('products.show', ['product' => $product->id]) }}">
                                                {{$product->title}}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="bottom">
                                        <div class="sold_count">销量 <span>{{$product->sold_count}}笔</span></div>
                                        <div class="review_count">评价 <span>{{$product->review_count}}</span></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="float-right">{{ $products->appends($filters)->render() }}</div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('scriptsAfterJs')
    <script>
        var filters = {!! json_encode($filters) !!};
        $(document).ready(function () {
            $('.search-form input[name=search]').val(filters.search);
            $('.search-form select[name=order]').val(filters.order);

            $('.search-form select[name=order]').on('change', function() {
                var searches = parseSearch();

                // 如果有属性筛选
                if (searches['filters']) {
                    // 将属性筛选值放入隐藏字段中
                    $('.search-form input[name=filters]').val(searches['filters']);
                }

                $('.search-form').submit();
            });
        })

        //生成key=>value
        function parseSearch() {
            var searches = {};

            location.search.substr(1).split('&').forEach(function (str) {
               var result = str.split('=');

               searches[decodeURIComponent(result[0])] = decodeURIComponent(result[1]);
            });

            return searches;
        }

        function buildSearch(searches) {
            var query = '?';

            _.forEach(searches, function (value, key) {
               query += encodeURIComponent(key) + '=' + encodeURIComponent(value) + '&';
            });

            // 去除最末尾的 & 符号
            return query.substr(0, query.length -1 );
        }

        function appendFilterToQuery(name, value) {
            var searches = parseSearch();

            if (searches['filters']) {
                //追加
                searches['filters'] += '|' + name + ':' + value;
            } else {
                // 否则初始化 filters
                searches['filters'] = name + ':' + value;
            }

            // 重新构建查询参数，并触发浏览器跳转
            location.search = buildSearch(searches);
        }

        function removeFilterFromQuery(name) {
            var searches = parseSearch();
            // 如果没有 filters 查询则什么都不做
            if(!searches['filters']) {
                return;
            }

            var filters = [];

            searches['filters'].split('|').forEach(function (filter) {
               var result = filter.split(':');

                // 如果属性名已存在filters中
               if (result[0] === name) {
                   return;
               }

               filters.push(filter);
            });

            searches['filters'] = filters.join('|');

            location.search = buildSearch(searches);
        }
    </script>
@stop