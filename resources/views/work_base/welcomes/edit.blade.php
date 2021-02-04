@extends('layouts')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>编辑成员欢迎语</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('work.base.welcomes.update',['w_wlcm_id' => $workWelcome->w_wlcm_id]) }}" method="POST">
        {{ method_field('put') }}
        @include('work_base.welcomes._form')
    </form>
@endsection

@section('script')
    @include('work_base.welcomes._js')
@endsection



