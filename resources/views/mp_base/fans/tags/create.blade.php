@extends('layouts')

@section('title','添加粉丝标签')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加粉丝标签</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('mp.base.fans.tags.store') }}" method="POST">
        @include('mp_base.fans.tags._form')
    </form>
@endsection

@section('script')
    @include('mp_base.fans.tags._js')
@endsection
