@extends('layouts')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加成员欢迎语</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('work.base.welcomes.store') }}" method="POST">
        @include('work_base.welcomes._form')
    </form>
@endsection

@section('script')
    @include('work_base.welcomes._js')
@endsection



