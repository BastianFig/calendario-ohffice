<!DOCTYPE html>
<html lang="es">
<head>
  <style>
    [x-cloak] {
      display: none !important;
    }
    </style>
    
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ohffice - Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link rel="icon" type="image/png" href="{{ asset('images/logo Oh_trans.png') }}">

</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
  <main class="p-6 flex-1">
    @yield('content')
  </main>
  
  @yield('scripts')
</body>
</html>
