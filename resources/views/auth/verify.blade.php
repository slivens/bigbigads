@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 ">
            @if (isset($error))
            <div class="alert alert-danger">{{$error}}</div>
            @else
            <div class="alert alert-success">{{$user->name}}, You email "{{$user->email}}" has verified, click <a href="/login">Here</a> to Login!</div>
            @endif
        </div>
    </div>
</div>
@endsection
