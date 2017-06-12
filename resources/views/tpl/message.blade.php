@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 ">
            @if (isset($desc))
            <div class="alert alert-{{$type}}">{{$desc}}</div>
            @endif
        </div>
    </div>
</div>
@endsection
