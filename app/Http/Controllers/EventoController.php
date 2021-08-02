<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;
use App\Repositories\EventoRepository;
use Validator;

class EventoController extends Controller
{

    protected $eventos;

    public function __construct(EventoRepository $eventos)
    {
        $this->eventos = $eventos;
    }

    public function AddView()
    {
        $convidados = User::whereNotIn('id',[Auth::id()])->get();
        //dd($convidados);
        return view('EventoForm',['convidados' => $convidados]);
    }

    public function EditView($id)
    {
        $convidados = User::whereNotIn('id',[Auth::id()])->get();
        $evento = Evento::find($id);
        return view('EventoForm',
        ['convidados' => $convidados, 
        'action' => 'edit',
        'evento' => $evento]);
    }

    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'descricao' => 'required|string',
            'data' => 'required',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors, 'success' => false]);
        }

        $this->eventos->store($request->all() + ['organizador_id' => Auth::id()]);

        return response()->json(['message' => "Registro salvo com sucesso", 'success' => true]);
    }

    public function listar(Request $request)
    {
        return $this->eventos->all();
    }
}
