@extends('layouts')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加短链接</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('mp.advanced.short_urls.store') }}" method="POST">
        @include('mp_advanced.short_urls._form')
    </form>
@endsection

@section('script')
    @include('mp_advanced.short_urls._js')
@endsection



