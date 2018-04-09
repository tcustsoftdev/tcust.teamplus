<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
   
        
  
    <title>公告訊息通知</title>
    

    <!-- Fonts -->
   
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/fontawesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
   
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
		html * {
			font-family: "微軟正黑體", "Lato", "Helvetica Neue", Helvetica, Arial, sans-serif;
		}
	</style>
</head>
<body>
    
    <div id="main" class="container body-content" style="min-height: 600px;">

        @yield('content')

    </div>
    <hr />
    <footer class="container">
        <p>&copy; 2018 - 慈濟科大電算中心</p>

        <div id="footer" ></div>
        
    </footer>
   
    <script src="{{ asset('js/babel.min.js') }}" ></script>
    <script src="{{ asset('js/polyfill.js') }}" ></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script src="{{ asset('css/bootstrap/dist/js/bootstrap.min.js') }}"></script>

    @yield('scripts')   
   
</body>
</html>
