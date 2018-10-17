@extends('layouts.app')
@section('title', '商品列表')

@section('content')
    <div class="row">
        <div class="col-lg-10 ">

            <div class="card">
                <div class="card-body">


                    <div class="row">
                            <form action="{{route('products.index')}}" class="form-inline search-form">
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
                                <div class="form-group">
                                    <input type="text" class="form-control input-sm" name="search" placeholder="搜索">
                                </div>

                                <div class="form-group" style="margin-left: 1rem">
                                    <button class="btn btn-primary btn-sm">搜索</button>
                                </div>

                                <div class="form-group" style="margin-left: 10rem">
                                    <select name="order" class="form-control input-sm float-right">
                                        <option value="">排序方式</option>
                                        <option value="price_asc">价格从低到高</option>
                                        <option value="price_desc">价格从高到低</option>
                                        <option value="sold_count_desc">销量从高到低</option>
                                        <option value="sold_count_asc">销量从低到高</option>
                                        <option value="rating_desc">评价从高到低</option>
                                        <option value="rating_asc">评价从低到高</option>
                                    </select>
                                </div>
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
                $('.search-form').submit();
            });
        })
    </script>
@stop