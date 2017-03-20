<?php
use \Illuminate\Support\Facades\Input;
?>
@extends('layouts.app')
<style>
    a#braintree-paypal-button {margin-top: 48px;}
</style>
@section('content')
<div class="container">
    @if ($errors->has('message'))
        <div class="alert alert-danger">
             {{ $errors->first('message') }}
        </div>
    @endif
    <form action="{{url('/pay')}}" method="post" id="checkout">
        {{ csrf_field() }}
    <input  type="hidden" name="planid" value="{{$plan->id}}"/>
    <div class="form-group">
            <label for="plan">
                Plan
            </label>
        <input type="text" class="form-control" value="{{$plan->display_name}}" name="plan" readonly>
    </div>

    <div class="form-group">
            <label for="price">
                Price:<span>{{$plan->currency}} {{$plan->amount}}</span>
            </label>
    </div>
    <!--
    <div class="row">
            <div id="paypal" class="col-sm-12 col-md-6"  aria-live="assertive" style="">
            </div>

            <a id="cc" class="col-sm-12 col-md-6 btn btn-secondary btn-green"  href="" title="Click to pay by Credit Card">
                Pay By Credit Card
            </a>
    </div>
    <div id="cc-info"  aria-hidden="true">
            <div class="form-group">
                <label for="number">
                    Credit Card Number
                </label>
                <div id="number" class="form-control"></div>
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                        <label for="expiration-date">
                            Expiration Date
                        </label>
                        <div id="expiration-date" class="form-control"></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                        <label for="cvv">
                            Secret Code (CVV)
                        </label>
                        <div id="cvv" class="form-control"></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                        <label>
                            Postal Code
                        </label>
                        <div id="postal-code" class="form-control"></div>
                    </div>
                </div>
            </div>

    </div>
    -->
        <div class="row">
            <input type="submit" value="Pay(Paypal)" class="btn btn-primary  btn-block" />
            <div class="help-block ">
                You can cancel, or change your plan at any time.
            </div>
        </div>
    </form>
</div>

@endsection
@section('script')
@endsection

