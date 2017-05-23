@extends('voyager::master')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-play"></i> 群发邮件
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
@if (session('sent')) 
<h3>群发到以下用户:</h3>
<div class="alert alert-info">
    @foreach (session('sent') as $item)
    <span>{{$item}},</span>
    @endforeach
</div>
@endif
                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">User Role</h3>
                    </div>

                    <div class="panel-body" style="padding-top:0;">
<form action="/edm/send" method="post" class="form-horizontal">
                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}
<div class="form-group">
    <label class="control-label col-sm-2">邮件</label>
    <div class="col-sm-10">
        @foreach(array_keys($mails) as $key) 
        <div class="radio">
            <label for="{{$key}}">
                <input type="radio"  value="{{$key}}" name="mail" id="{{$key}}" required>
                {{$mails[$key]['name']}}
            </label>
        </div>
        @endforeach
    </div>
</div>
<div class="form-group">
    <label class="control-label col-sm-2">邮箱勾选</label>
    <div class="col-sm-10">

        @foreach(array_keys($mailTypes) as $key) 
		<div class="checkbox3">
		  <input type="checkbox" id="{{$key}}" name="mailType[]" value="{{$key}}">
		  <label for="{{$key}}">
			{{$mailTypes[$key]}}
		  </label>
		</div>
        @endforeach
        <div class="help-block">* 重复的邮件只会发送一次</div>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-sm-2">发送间隔</label>
    <div class="col-sm-10">
        <input type="number" class="text form-control" value="20" name="interval">
        <div class="help-block">* 发送间隔是为了防止达到收件方ISP的最大限制，一般使用默认值`</div>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-primary">群发</button>
    </div>
</div>
</form>
                    </div>


                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script type="text/javascript">
$(function() {
    $('#selectBtn').click(function() {
        var val = $(this).data('val');
        if (Number(val) === 0) {
			//美化后的Checkbox的复选框是个图片，所以只能通过点击触发的方式实现反转,只修改checkbox是不行的
            $('[name=maillist]').each(function() {
                $(this).attr('checked', false);
				$(this).trigger('click');
            });//attr('checked', 'checked');        
            $(this).data('val', 1);
            $(this).text("取消");
        } else {
            $('[name=maillist]').each(function() {
                $(this).attr('checked', false);
            })
            $(this).data('val', 0);
            $(this).text("全选");
        }
    });
});
</script>
@stop
