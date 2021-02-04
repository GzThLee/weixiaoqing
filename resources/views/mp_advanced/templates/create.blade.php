@extends('layouts')
@section('title','模板消息添加')
@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加模板消息</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('mp.advanced.templates.store') }}" method="POST">
        @include('mp_advanced.templates._form')
    </form>
@endsection
@section('script')
    @include('mp_advanced.templates._js')
@endsection
