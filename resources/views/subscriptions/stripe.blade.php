<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Card</title>
    <script type="text/javascript">
       // window.laravel.stripeKey = "{{$key}}";
    </script>
<style>

.StripeElement {
  background-color: white;
  padding: 8px 12px;
  border-radius: 4px;
  border: 1px solid transparent;
  box-shadow: 0 1px 3px 0 #e6ebf1;
  -webkit-transition: box-shadow 150ms ease;
  transition: box-shadow 150ms ease;
}

.StripeElement--focus {
  box-shadow: 0 1px 3px 0 #cfd7df;
}

.StripeElement--invalid {
  border-color: #fa755a;
}

.StripeElement--webkit-autofill {
  background-color: #fefde5 !important;
}
/*
.stripe-card {
  width: 300px;
  border: 1px solid grey;
}
.stripe-card.complete {
  border-color: green;
}*/
</style>
</head>
<body>
    <div id="app">
    <h1>Please give us your payment details:</h1>
            @if ($errors->has('message'))
            <div class="alert alert-danger">
                 {{ $errors->first('message') }}
            </div>
            @endif
            <form v-on:submit.prevent ref="payform" action="https://bigbigads.dev/pay" method="post" id="payment-form">
                {{ csrf_field() }}

                <input type="hidden"  v-model="token" name="stripeToken">
                <input type="hidden"  value="5" name="planid">
                <input type="hidden"  value="stripe" name="payType">
                <div class="form-row">
                    <label for="card-element">
                        Credit or debit card
                    </label>
                    <card class='stripe-card'
                           :class='{ complete }'
                           stripe='{{$key}}'
                           :options='stripeOptions'
                           @change='complete = $event.complete'
                           />
                </div>

                <button @click='pay' :disabled='!complete' >Submit Payment</button>

                <button @click='submit' :disabled='!complete' >Real Submit </button>
            </form>
    </div>

    <!-- The required Stripe lib -->
    <script type="text/javascript" src="https://js.stripe.com/v3/"></script>

    <script type="text/javascript" src="/dist/vendor.js"></script>
    <script type="text/javascript" src="/dist/stripe.js"></script>

</body>
</html>
