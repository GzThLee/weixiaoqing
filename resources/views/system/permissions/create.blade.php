@extends('layouts')

@section('style')
    <link rel="stylesheet" href="{{ asset('layuimini/lib/font-awesome-4.7.0/css/font-awesome.min.css') }}" media="all">
    <style>
        .layui-iconpicker-body {
            width: 500px;
        }
    </style>
@endsection

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加权限</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('system.permissions.store')}}" method="post">
                @include('system.permissions._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('system.permissions._js')
@endsection
