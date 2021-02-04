@extends('layouts')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加渠道二维码</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('work.advanced.channel_codes.store') }}" method="POST">
        @include('work_advanced.channel_codes._form')
    </form>
@endsection

@section('script')
    @include('work_advanced.channel_codes._js')
@endsection



