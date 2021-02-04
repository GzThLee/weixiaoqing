@extends('layouts')

@section('title','编辑粉丝标签')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>编辑粉丝标签</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('mp.base.fans.tags.update',['m_tag_id' => $tag->m_tag_id]) }}" method="POST">
        {{ method_field('put') }}
        @include('mp_base.fans.tags._form')
    </form>
@endsection

@section('script')
    @include('mp_base.fans.tags._js')
@endsection
