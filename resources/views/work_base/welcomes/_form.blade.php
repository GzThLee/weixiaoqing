{{ csrf_field() }}
<div class="layui-form-item">
    <label class="layui-form-label">欢迎语名称:</label>
    <div class="layui-input-block">
        <input type="text" name="name" lay-verify="required" value="{{ $workWelcome->name ?? old('name','') }}"
               placeholder="请输入欢迎语名称" class="layui-input">
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">选择成员:</label>
    <div class="layui-input-block">
        <select name="w_user_id" lay-filter="w_user_id" lay-search>
            <option value=""></option>
            @foreach(\App\Models\WorkUser::pluck('name','w_user_id') as $id => $name)
                <option value="{{$id}}" @if(($workWelcome->w_user_id ?? old('w_user_id',0)) == $id) selected @endif>{{$name}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="layui-form-item layui-form-text">
    <label class="layui-form-label">内容1：</label>
    <div class="layui-input-block">
        <textarea placeholder="请输入内容" name="content" class="layui-textarea">{{ $workWelcome->text->content ?? old('content','') }}</textarea>
    </div>
</div>

<div class="layui-form-item layui-form-text">
    <input type="hidden" value="{{$workWelcome->welcome_type ?? old('welcome_type',0)}}" name="welcome_type" id="welcome-type">
    <label class="layui-form-label">内容2：</label>
    <div class="layui-input-block">
        <div class="layui-tab layui-tab-brief" lay-filter="type">
            <ul class="layui-tab-title">
                <li @if(($workWelcome->welcome_type ?? old('welcome_type',0)) == 0) class="layui-this" @endif>无(内容1)</li>
                <li @if(($workWelcome->welcome_type ?? old('welcome_type',0)) == 1) class="layui-this" @endif>图片</li>
                <li @if(($workWelcome->welcome_type ?? old('welcome_type',0)) == 2) class="layui-this" @endif>图文链接</li>
                <li @if(($workWelcome->welcome_type ?? old('welcome_type',0)) == 3) class="layui-this" @endif>小程序</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item @if(($workWelcome->welcome_type ?? old('welcome_type',0)) == 0) layui-show @endif"></div>
                <div class="layui-tab-item @if(($workWelcome->welcome_type ?? old('welcome_type',0)) == 1) layui-show @endif">
                    <div class="layui-form-item">
                        <label class="layui-form-label">图片:</label>
                        <div class="layui-input-block">
                            <div class="layui-upload">
                                <div class="layui-upload-list">
                                    <img class="layui-upload-img" src="{{ $workWelcome->image->pic_url ?? old('image["pic_url"]','https://via.placeholder.com/120X120') }}" id="image-image" width="150" height="auto">
                                </div>
                                <button type="button" class="layui-btn" id="image-btn">上传图片</button>
                            </div>
                            <input type="hidden" name="image[pic_url]" value="{{ $workWelcome->image->pic_url ?? old('image["pic_url"]','') }}" id="image-input">
                        </div>
                    </div>
                </div>
                <div class="layui-tab-item @if(($workWelcome->welcome_type ?? old('welcome_type',0)) == 2) layui-show @endif">
                    <div class="layui-form-item">
                        <label class="layui-form-label">消息标题:</label>
                        <div class="layui-input-block">
                            <input type="text" name="link[title]" value="{{ $workWelcome->link->title ?? old('link["title"]','') }}"
                                   placeholder="请输入欢迎语名称" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">消息封面:</label>
                        <div class="layui-input-block">
                            <div class="layui-upload">
                                <div class="layui-upload-list">
                                    <img class="layui-upload-img" src="{{ $workWelcome->link->picurl ?? old('link["picurl"]','https://via.placeholder.com/120X120') }}" id="link-image" width="150" height="auto">
                                </div>
                                <button type="button" class="layui-btn" id="link-btn">上传图片</button>
                            </div>
                            <input type="hidden" name="link[picurl]" value="{{ $workWelcome->link->picurl ?? old('link["picurl"]','') }}" id="link-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">消息描述:</label>
                        <div class="layui-input-block">
                            <input type="text" name="link[desc]" value="{{ $workWelcome->link->desc ?? old('link["desc"]','') }}"
                                   placeholder="请输入消息描述" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">链接:</label>
                        <div class="layui-input-block">
                            <input type="text" name="link[url]" value="{{ $workWelcome->link->url ?? old('link["url"]','') }}"
                                   placeholder="请输入链接地址" class="layui-input">
                        </div>
                    </div>
                </div>
                <div class="layui-tab-item @if(($workWelcome->welcome_type ?? old('welcome_type',0)) == 3) layui-show @endif">
                    <div class="layui-form-item">
                        <label class="layui-form-label">消息标题:</label>
                        <div class="layui-input-block">
                            <input type="text" name="miniprogram[title]" value="{{ $workWelcome->miniprogram->title ?? old('miniprogram["title"]','') }}" placeholder="请输入欢迎语名称" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">消息封面:</label>
                        <div class="layui-input-block">
                            <div class="layui-upload">
                                <div class="layui-upload-list">
                                    <img class="layui-upload-img" src="{{ $workWelcome->miniprogram->mediaid ?? old('miniprogram["mediaid"]','https://via.placeholder.com/120X120') }}"
                                         id="miniprogram-image" width="150" height="auto">
                                </div>
                                <button type="button" class="layui-btn" id="miniprogram-btn">上传图片</button>
                            </div>
                            <input type="hidden" name="miniprogram[mediaid]" value="{{ $workWelcome->miniprogram->mediaid ?? old('miniprogram["mediaid"]','') }}" id="miniprogram-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">appid:</label>
                        <div class="layui-input-block">
                            <input type="text" name="miniprogram[appid]" value="{{ $workWelcome->miniprogram->appid ?? old('miniprogram["appid"]','') }}"
                                   placeholder="请输入欢迎语名称" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">路径:</label>
                        <div class="layui-input-block">
                            <input type="text" name="miniprogram[path]" value="{{ $workWelcome->miniprogram->path ?? old('miniprogram["path"]','') }}"
                                   placeholder="请输入欢迎语名称" class="layui-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="" lay-filter="submit">保存</button>
        <a href="{{ route('work.base.welcomes') }}" class="layui-btn layui-btn-normal" >返回</a>
    </div>
</div>



