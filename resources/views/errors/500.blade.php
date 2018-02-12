@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 ">
            <div class="alert alert-danger">
            {!! $exception->getMessage() !!}
            </div>
        </div>
    </div>
</div>
@endsection
