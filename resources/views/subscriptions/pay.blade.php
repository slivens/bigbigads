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
    <input  type="hidden" name="payment-method-nonce"/>
    <div class="form-group">
            <label for="plan">
                Plan
            </label>
        <input type="text" class="form-control" value="{{$plan->id}}" name="plan" readonly>
    </div>

    <div class="form-group">
            <label for="price">
                Price:<span>{{$plan->price}}</span>
            </label>
    </div>
    <div class="row">
            <div id="paypal" class="col-sm-12 col-md-6"  aria-live="assertive" style="">


<script src="https://www.paypalobjects.com/api/button.js?"
     data-merchant="braintree"
     data-id="paypal-button"
     data-button="checkout"
     data-color="gold"
     data-size="medium"
     data-shape="pill"
     data-button_type="submit"
     data-button_disabled="false"
 ></script>
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
            <div class="row">
                <input type="submit" value="Pay" class="btn-primary btn-orange col-sm-12" />
                <div class="help-block ">
                    You can cancel, or change your plan at any time.
                </div>
            </div>
        </div>
        </form>
</div>
@endsection
@section('script')
<script src="https://js.braintreegateway.com/web/3.6.3/js/client.min.js"></script>
<script type="text/javascript" src="https://js.braintreegateway.com/web/3.6.3/js/hosted-fields.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.6.3/js/paypal.min.js"></script>
<script>
$('#cc').on( 'click', function(e) {
    $( '#cc-info' ).show().attr( 'aria-hidden', true ).css( 'visibility', 'visible' );
});

var url = "<?php echo url('subscription' ); ?>";
var form = document.querySelector('#checkout');
var submit = document.querySelector('input[type=submit]');
var paypalButton = document.querySelector('.paypal-button');
braintree.client.create({
authorization:"{{$clientToken}}"
}, function(clientErr, clientInstance) {
    if (clientErr) {
        console.log(clientErr);
        return;
    }
    braintree.hostedFields.create({
    client:clientInstance,
        fields:{
        number: {
        selector: "#number"
    },
        postalCode: {
        selector: '#postal-code'
    },
        expirationDate: {
        selector: "#expiration-date",
            placeholder: "00/00"
    },
        cvv: {
        selector: "#cvv"
    }
    }}, function(hostedFieldErr, hostedFieldsInstance) {
        if (hostedFieldErr)
            return;
        submit.removeAttribute('disabled');

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            submit.addAttribute('disabled');
            hostedFieldsInstance.tokenize(function (tokenizeErr, payload) {
                if (tokenizeErr) {
                    submit.removeAttribute('disabled');
                    console.log("err", tokenizeErr);
                    // Handle error in Hosted Fields tokenization
                    return;
                }

                // Put `payload.nonce` into the `payment-method-nonce` input, and then
                // submit the form. Alternatively, you could send the nonce to your server
                // with AJAX.
                document.querySelector('input[name="payment-method-nonce"]').value = payload.nonce;
                form.submit();
            });
        }, false);
    }
);
    // Create a PayPal component.
    /* braintree.paypal.create({ */
    /* client: clientInstance */
/* }, function (paypalErr, paypalInstance) { */

    /* // Stop if there was a problem creating PayPal. */
    /* // This could happen if there was a network error or if it's incorrectly */
    /* // configured. */
    /* if (paypalErr) { */
    /*     console.error('Error creating PayPal:', paypalErr); */
    /*     return; */
    /* } */

    /* // Enable the button. */
    /* paypalButton.removeAttribute('disabled'); */

    /* // When the button is clicked, attempt to tokenize. */
    /* paypalButton.addEventListener('click', function (event) { */

    /*     // Because tokenization opens a popup, this has to be called as a result of */
    /*     // customer action, like clicking a button—you cannot call this at any time. */
    /*     paypalInstance.tokenize({ */
    /*     flow: 'vault' */
    /* }, function (tokenizeErr, payload) { */

    /*     // Stop if there was an error. */
    /*     if (tokenizeErr) { */
    /*         if (tokenizeErr.type !== 'CUSTOMER') { */
    /*             console.error('Error tokenizing:', tokenizeErr); */
    /*         } */
    /*         return; */
    /*     } */

    /*     // Tokenization succeeded! */
    /*     paypalButton.setAttribute('disabled', true); */
    /*     console.log('Got a nonce! You should submit this to your server.'); */
    /*     console.log(payload.nonce); */

    /* }); */

    /* }, false); */

/* }); */

});
</script>
@endsection

