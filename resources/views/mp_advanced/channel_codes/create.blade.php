@extends('layouts')

@section('title','创建渠道二维码')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>创建渠道二维码</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('mp.advanced.channel_codes.store') }}" method="POST">
        @include('mp_advanced.channel_codes._form')
    </form>
@endsection

@section('script')
    @include('mp_advanced.channel_codes._js')
@endsection
