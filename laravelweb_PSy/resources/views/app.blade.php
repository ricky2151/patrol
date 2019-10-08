<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name='csrf-token' content="{{csrf_token()}}">
        <title>Laravel</title>

        <!-- Vuetify CSS -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons" rel="stylesheet">
        
        <link href="{{asset('css/app.css')}}"/>

       
    </head>
    <body>
       <div id="app">
        <div id='myLoading'style='display:none;width:100%;height:100%;background-color: rgba(0,0,0,0.5);position: fixed;z-index: 9999999'>
          <img src='/assets/images/loading.gif' style='position: absolute;top:50%;left: 50%;transform: translate(-50%,-50%);'>
        </div>
           <patrol-app></patrol-app>
       </div>
       <script src="{{asset('js/app.js')}}"></script>
    </body>
</html>
