<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <title>Agenda Ohffice - Nuevo Evento</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        color: #333;
      }
      .container {
        max-width: 600px;
        margin: 20px auto;
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 20px;
      }
      .header {
        text-align: center;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
      }
      .header h1 {
        margin: 0;
        font-size: 24px;
        color: #007bff;
      }
      .content {
        margin-top: 20px;
      }
      .content h2 {
        font-size: 20px;
        color: #333;
        margin-bottom: 10px;
      }
      .content p {
        font-size: 16px;
        line-height: 1.5;
        margin: 8px 0;
      }
      .footer {
        margin-top: 30px;
        text-align: center;
        font-size: 12px;
        color: #888;
        border-top: 1px solid #eee;
        padding-top: 10px;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="header">
        <h1>Agenda Ohffice</h1>
      </div>
      <div class="content">
        <h2>Nuevo Evento Registrado</h2>
        <p><strong>Nombre del Solicitante:</strong> {{ $evento->usuario->nombre }}</p>
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($evento->fecha)->format('d/m/Y') }}</p>
        <p><strong>Descripción del Evento:</strong></p>
        <p>{{ $evento->descripcion }}</p>
      </div>
      <div class="footer">
        <p>Este mensaje es generado automáticamente por Agenda Ohffice.</p>
      </div>
    </div>
  </body>
</html>
