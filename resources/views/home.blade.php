@extends('layouts')

@section('style')
    <style>
        .layui-card {border:1px solid #f2f2f2;border-radius:5px;}
        .icon {margin-right:10px;color:#1aa094;}
        .icon-tip {color:#ff5722!important;}
        .layuimini-qiuck-module a i {display:inline-block;width:100%;height:60px;line-height:60px;text-align:center;border-radius:2px;font-size:30px;background-color:#F8F8F8;color:#333;transition:all .3s;-webkit-transition:all .3s;}
        .layuimini-qiuck-module a cite {position:relative;top:2px;display:block;color:#666;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;font-size:14px;}
        .welcome-module {width:100%;height:210px;}
        .panel {background-color:#fff;border:1px solid transparent;border-radius:3px;-webkit-box-shadow:0 1px 1px rgba(0,0,0,.05);box-shadow:0 1px 1px rgba(0,0,0,.05)}
        .panel-body {padding:10px}
        .panel-title {margin-top:0;margin-bottom:0;font-size:12px;color:inherit}
        .label {display:inline;padding:.2em .6em .3em;font-size:75%;font-weight:700;line-height:1;color:#fff;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:.25em;margin-top: .3em;}
        .main_btn > p {height:40px;}
        .layui-bg-number {background-color:#F8F8F8;}
        .layuimini-notice:hover {background:#f6f6f6;}
        .layuimini-notice {padding:7px 16px;clear:both;font-size:12px !important;cursor:pointer;position:relative;transition:background 0.2s ease-in-out;}
        .layuimini-notice-title,.layuimini-notice-label {
            padding-right: 70px !important;text-overflow:ellipsis!important;overflow:hidden!important;white-space:nowrap!important;}
        .layuimini-notice-title {line-height:28px;font-size:14px;}
        .layuimini-notice-extra {position:absolute;top:50%;margin-top:-8px;right:16px;display:inline-block;height:16px;color:#999;}
    </style>
@endsection

@section('content')
    <div class="layui-row layui-col-space15">
        <div class="layui-card">
            <div class="layui-card-header"><i class="fa fa-warning icon"></i>数据统计</div>
            <div class="layui-card-body">
                <div class="welcome-module">
                    <div class="layui-row layui-col-space10">
                        <div class="layui-col-xs6">
                            <div class="panel layui-bg-number">
                                <div class="panel-body">
                                    <div class="panel-title">
                                        <span class="label pull-right layui-bg-green">实时</span>
                                        <h5>公众号粉丝数</h5>
                                    </div>
                                    <div class="panel-content">
                                        <h1 class="no-margins">{{$fansCount}}</h1>
                                        <small>关注粉丝</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-xs6">
                            <div class="panel layui-bg-number">
                                <div class="panel-body">
                                    <div class="panel-title">
                                        <span class="label pull-right layui-bg-green">实时</span>
                                        <h5>公众号活跃粉丝数</h5>
                                    </div>
                                    <div class="panel-content">
                                        <h1 class="no-margins">{{$fansActCount}}</h1>
                                        <small>活跃用户:48小时内与公众号有交互粉丝</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-xs4">
                            <div class="panel layui-bg-number">
                                <div class="panel-body">
                                    <div class="panel-title">
                                        <span class="label pull-right layui-bg-green">实时</span>
                                        <h5>企业微信成员数</h5>
                                    </div>
                                    <div class="panel-content">
                                        <h1 class="no-margins">{{$userCount}}</h1>
                                        <small>企业微信员工</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-xs4">
                            <div class="panel layui-bg-number">
                                <div class="panel-body">
                                    <div class="panel-title">
                                        <span class="label pull-right layui-bg-green">实时</span>
                                        <h5>企业微信员工客户数</h5>
                                    </div>
                                    <div class="panel-content">
                                        <h1 class="no-margins">{{$userCustomerCount}}</h1>
                                        <small>全部员工客户</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-xs4">
                            <div class="panel layui-bg-number">
                                <div class="panel-body">
                                    <div class="panel-title">
                                        <span class="label pull-right layui-bg-green">实时</span>
                                        <h5>企业微信员工客户群数</h5>
                                    </div>
                                    <div class="panel-content">
                                        <h1 class="no-margins">{{$userCustomerGroupCount}}</h1>
                                        <small>全部员工客户群</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer', 'table', 'form'], function () {
            var $ = layui.jquery
                , layer = layui.layer
                , form = layui.form;

            /**
             * 查看公告信息
             **/
            $('body').on('click', '.layuimini-notice', function () {
                var title = $(this).children('.layuimini-notice-title').text(),
                    noticeTime = $(this).children('.layuimini-notice-extra').text(),
                    content = $(this).children('.layuimini-notice-content').html();
                var html = '<div style="padding:15px 20px; text-align:justify; line-height: 22px;border-bottom:1px solid #e2e2e2;background-color: #2f4056;color: #ffffff">\n' +
                    '<div style="text-align: center;margin-bottom: 20px;font-weight: bold;border-bottom:1px solid #718fb5;padding-bottom: 5px"><h4 class="text-danger">' + title + '</h4></div>\n' +
                    '<div style="font-size: 12px">' + content + '</div>\n' +
                    '</div>\n';
                parent.layer.open({
                    type: 1,
                    title: '系统公告'+'<span style="float: right;right: 1px;font-size: 12px;color: #b1b3b9;margin-top: 1px">'+noticeTime+'</span>',
                    area: '500px;',
                    shade: 0.6,
                    id: 'layuimini-notice',
                    btn: ['关闭'],
                    btnAlign: 'c',
                    moveType: 1,
                    content:html,
                    success: function (layero) {
                        layer.closeAll();
                    }
                });
            });

        })
    </script>
@endsection



