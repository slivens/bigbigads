@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 ">
            @if (isset($error))
            <div class="alert alert-danger">{{$error}}</div>
            @if (isset($link)) 
                <div>If you have not received the email, click <a href="{{$link}}">Here</a> to send again.</div>
            @endif
            @elseif (isset($info))
            <div class="alert alert-info">{{$info}}</div>
            @else
            <div class="alert alert-success">{{$user->name}}, You email "{{$user->email}}" has verified, click <a href="/login">Here</a> to Login!</div>
            @endif
        </div>
    </div>
</div>
@endsection
