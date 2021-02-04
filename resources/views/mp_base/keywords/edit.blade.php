@extends('layouts')

@section('title','编辑关键字')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>编辑关键字</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('mp.base.keywords.update',['m_kw_id' => $keyword->m_kw_id]) }}" method="POST">
        {{ method_field('put') }}
        @include('mp_base.keywords._form')
    </form>
@endsection

@section('script')
    @include('mp_base.keywords._js')
@endsection
