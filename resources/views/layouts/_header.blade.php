<nav class="navbar navbar-expand-md  navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#app-navbar-collapse">
                &#9776;
            </button>
            <a class="navbar-brand" href="{{ url('/') }}">
                Laravel Shop
            </a>
        </div>
        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <ul class="nav navbar-nav">
                @if(isset($categoryTree))
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">所有类目<b class="caret"></b></a>
                        <ul class="dropdown-menu multi-level">
                            @each('layouts._category_item', $categoryTree, 'category')
                        </ul>
                    </li>
                @endif
            </ul>
            <ul class="nav navbar-nav ">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('login')}}">登录</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('register')}}">注册</a>
                    </li>
                    @else
                        <li class="nva-item">
                            <a href="{{ route('cart.index') }}">购物车<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span></a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <span class="user-avatar pull-left" style="margin-right:8px; margin-top:-5px;">
                                <img src="https://fsdhubcdn.phphub.org/uploads/images/201709/20/1/PtDKbASVcz.png?imageView2/1/w/60/h/60" class="img-responsive img-circle" width="30px" height="30px">
                            </span>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ route('user_addresses.index') }}">收货地址</a>
                                </li>
                                <li>
                                    <a href="{{route('orders.index')}}">我的订单</a>
                                </li>
                                <li>
                                    <a href="{{ route('installments.index') }}">分期付款</a>
                                </li>
                                <li>
                                    <a href="{{ route('products.favorites') }}">我的收藏</a>
                                </li>
                                <li>
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                        退出登录
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>


                        @endguest

            </ul>
        </div>
    </div>
</nav>