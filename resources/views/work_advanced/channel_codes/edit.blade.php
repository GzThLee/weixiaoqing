@extends('layouts')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>编辑渠道二维码</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('work.advanced.channel_codes.update',['w_ccode_id' => $workChannelCode->w_ccode_id]) }}" method="POST">
        {{ method_field('put') }}
        @include('work_advanced.channel_codes._form')
    </form>
@endsection

@section('script')
    @include('work_advanced.channel_codes._js')
@endsection



