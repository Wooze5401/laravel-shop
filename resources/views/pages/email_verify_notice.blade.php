@extends('layouts.app')
@section('title', 'notify')
@section('content')
<div class="card">
    <div class="card-header">提示</div>
    <div class="card-block">
        <h1>请先验证邮箱</h1>
        <a class="btn btn-primary" href="{{route('root')}}">返回首页</a>
    </div>
</div>
@stop