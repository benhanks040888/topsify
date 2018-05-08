<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Topsify</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.0/css/bulma.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.11/css/solid.css" integrity="sha384-XmxkDmPXx/Hc19URgeNR9YRtH392uNsw0o7nb6dfZieETZuPI2mz5E41KhXKMqWQ" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.11/css/fontawesome.css" integrity="sha384-JrS00hUlxRW4kN+mi4N+nYNvl2brci75f8X1VBQh/DbA/XIA2u8i3LLLxB68iE3R" crossorigin="anonymous">
  @yield('styles')
</head>
<body>
  <section class="section">
    <div class="container">
      {{-- <h1 class="title">Topsify</h1> --}}

      @yield('content')
    </div>
  </section>

  <script src="{{ asset('js/app.js') }}"></script>

  @yield('scripts')
</body>
</html>