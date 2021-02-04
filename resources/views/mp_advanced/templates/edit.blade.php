@extends('layouts')
@section('title','模板消息设置')
@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>编辑模板消息</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('mp.advanced.templates.update',['m_temp_id' => $template->m_temp_id]) }}" method="POST">
        {{ method_field('put') }}
        @include('mp_advanced.templates._form')
    </form>
@endsection
@section('script')
    @include('mp_advanced.templates._js')
@endsection
