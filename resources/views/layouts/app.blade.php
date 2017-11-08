<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Bigbigads') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="/static/images/favicon.ico" media="screen">
    <!-- Styles -->
    <link href="{{bba_version('home.css')}}" rel="stylesheet">
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    @include('tpl.header')
    <!-- Scripts -->
 <!--   <script src="/js/app.js"></script> -->
@yield('script')
</body>
</html>
