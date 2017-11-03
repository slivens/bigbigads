<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<style type="text/css">
.top{
    height: 150px;
    margin-top: 50px;
}
.top_left_img{
    float: left;
    width:300px;
}
.top_right_inc{
    float: right;
    width:300px;
}
.top_right_inc p{
    color:#b4b4c6;
}
.content_title p{
    color:#6B6B75;
    font-size:16px;
}
.row_left{
    width:35%;
    font-size: 16px;
    color:#9A9A9C;
}
.row_right{
    width:65%;
    font-size: 16px;
    
}
hr{
    border: 1px solid #C4C4C4;
}
.content{
    text-align: left;
}
.content_transaction,.content_method,.content_customer{
    width:500px;
}
</style>
<body>
    <div class="container">
        <div class="top">
            <div class="top_left_img">
                <img height="80" src="data:image/png;base64,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" alt="">
            </div>
            <div class="top_right_inc">
                <p>Bigbigads.Inc</p>
                <p>LUCKY CENTRE 171 WANCHAI ROAD</p>
                <P>Chai Wan,Hong Kong Island.</P>
            </div>
        </div>
        <div class="content">
            <div class="content_title">
                <h1><b>Invoice From BIGBIGADS</b></h1>
                <p>Your transaction is completed and processed securely.</p>
                <p>Please retain this copy for your records.</p>
            </div>
            <hr>
            <div class="content_transaction">
                <h3>TRANSACTION</h3>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Reference ID
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                            {{$data->referenceId}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Amount of payment
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                                {{$data->amount}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Date of payment
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                                {{$data->date}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Payment account
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                                {{$data->paymentAccount}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Package
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                                {{$data->package}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Expiration time
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                                {{$data->expirationTime}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="content_method">
                <h3>PAYMENT METHOD</h3>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Method
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                                Paypal
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="content_customer">
                <h3>CUSTOMER</h3>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Name
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                                {{$data->name}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Email
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                                {{$data->email}}
                            </div>
                        </div>
                        @if ($data->company_name)
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Company
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                                {{$data->company_name}}
                            </div>
                        </div>
                        @endif
                        @if ($data->address)
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Address
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                                {{$data->address}}
                            </div>
                        </div>
                        @endif
                        @if ($data->contact_info)
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Contact
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                                {{$data->contact_info}}
                            </div>
                        </div>
                        @endif
                        @if ($data->website)
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Website
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                                {{$data->website}}
                            </div>
                        </div>
                        @endif
                        @if ($data->tax_no)
                        <div class="row">
                            <div class="col-xs-8 col-sm-4 row_left">
                                Tax No
                            </div>
                            <div class="col-xs-4 col-sm-4 row_right">
                                {{$data->tax_no}}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>