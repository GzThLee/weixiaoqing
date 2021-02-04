@extends('layouts')

@section('title','添加关键字')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加关键字</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('mp.base.keywords.store') }}" method="POST">
        @include('mp_base.keywords._form')
    </form>
@endsection

@section('script')
    @include('mp_base.keywords._js')
@endsection
