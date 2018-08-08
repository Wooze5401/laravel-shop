@extends('layouts.app')
@section('title', 'notify')
@section('content')
<div class="card">
    <div class="card-header">提示</div>
    <div class="card-body">
        <h1>请先验证邮箱</h1>
        <a class="btn btn-primary" href="{{ route('email_verification.send') }}">重新发送验证邮件</a>
    </div>
</div>
@stop