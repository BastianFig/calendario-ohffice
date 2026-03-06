<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use App\Mail\EventoDetalleMail;
use Illuminate\Support\Facades\Mail;

class EventoController extends Controller
{
   
    public function index()
    {
        $eventos = Evento::with('usuario')->get();
        
        // CAMBIO CRÍTICO: Ahora incluye el año completo en fechaShort
        $eventos = $eventos->map(function ($evento) {
            $evento->fechaShort = Carbon::parse($evento->fecha)->format('d/m/Y');
            return $evento;
        });
       
        return response()->json($eventos);
    }

   
    public function store(Request $request)
    {
        $data = $request->validate([
            'usuario'      => 'required|string',
            'fecha'        => 'required|date_format:d/m/Y',
            'hora_inicio'  => 'nullable|date_format:H:i',
            'hora_fin'     => 'nullable|date_format:H:i',
            'descripcion'  => 'required|string|max:255',

        ]);

        $fecha = Carbon::createFromFormat('d/m/Y', $data['fecha'])->format('Y-m-d');

        Log::info('Guardando evento', [
            'usuario'     => $data['usuario'],
            'fecha'       => $fecha,
            'descripcion' => $data['descripcion']
        ]);

        $usuario = Usuario::where('nombre', $data['usuario'])->first();
        if (!$usuario) {
            Log::error('Usuario no encontrado: ' . $data['usuario']);
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $evento = Evento::create([
            'usuario_id'  => $usuario->id,
            'fecha'       => $fecha,
            'hora_inicio' => $data['hora_inicio'] ?? null,
            'hora_fin'    => $data['hora_fin'] ?? null,
            'descripcion' => $data['descripcion'],
        ]);
    
        Log::info('Evento guardado', $evento->toArray());
    
        if ($usuario->nombre !== 'Rodrigo Esparza') {
            Mail::to('rodrigo.esparza@ohffice.cl')->send(new EventoDetalleMail($evento));
        }
    
        return response()->json([
            'message' => 'Evento guardado exitosamente',
            'evento'  => $evento
        ], 201);
    }

   
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'descripcion' => 'required|string',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin'    => 'nullable|date_format:H:i',
        ]);

        $evento = Evento::find($id);
        if (!$evento) {
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }

        $evento->descripcion = $data['descripcion'];
        $evento->hora_inicio = $data['hora_inicio'] ?? null;
        $evento->hora_fin    = $data['hora_fin'] ?? null;
        $evento->save();

        return response()->json([
            'message' => 'Evento actualizado exitosamente',
            'evento'  => $evento,
        ]);
    }

   
    public function destroy($id)
    {
        $evento = Evento::find($id);
        if (!$evento) {
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }
        $evento->delete();
        return response()->json([
            'message' => 'Evento eliminado exitosamente'
        ]);
    }
}