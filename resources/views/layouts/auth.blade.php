<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Iniciar Sesión')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link rel="icon" type="image/png" href="{{ asset('images/logo Oh_trans.png') }}">

  <style>
    [x-cloak] { display: none !important; }
  </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
  @yield('content')
</body>
</html>
