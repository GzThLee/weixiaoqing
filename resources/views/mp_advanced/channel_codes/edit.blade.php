@extends('layouts')

@section('title','编辑渠道二维码')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>编辑渠道二维码</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('mp.advanced.channel_codes.update',['m_ccode_id' => $channelCode->m_ccode_id]) }}" method="POST">
        {{ method_field('put') }}
        @include('mp_advanced.channel_codes._form')
    </form>
@endsection
@section('script')
    @include('mp_advanced.channel_codes._js')
@endsection
