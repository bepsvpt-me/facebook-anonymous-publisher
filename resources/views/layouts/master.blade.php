<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>{{ $pageName or '匿名靠北' }}</title>
    <meta property="og:title" content="{{ $pageName or '匿名靠北' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:locale" content="zh_TW">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
    @stack('styles')
  </head>
  <body>
    <main class="container">
      <section style="margin: 20px 0;">
        @include('flash::message')
      </section>

      @yield('main')
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.10.2/validator.min.js" defer></script>
  </body>
</html>
