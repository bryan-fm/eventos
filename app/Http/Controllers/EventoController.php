<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;
use App\Repositories\EventoRepository;
use Validator;
use Carbon\Carbon;

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
        return view('EventoForm',
        ['convidados' => $convidados,
        'action' => 'add']);
    }

    public function EditView($id)
    {
        $convidados = User::whereNotIn('id',[Auth::id()])->get();
        $evento = $this->eventos->get($id);
        return view('EventoForm',
        ['convidados' => $convidados, 
        'action' => 'edit',
        'evento' => $evento]);
    }

    public function save(Request $request, $id = 0)
    {
        $validator = Validator::make($request->all(),[
            'descricao' => 'required|string',
            'data' => 'required',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors, 'success' => false]);
        }
        $retorno = $this->eventos->store($request->all() + ['organizador_id' => Auth::id()],$id);

        return response()->json(['message' => $retorno->original['message'], 'success' => $retorno->original['success']]);
    }

    public function listar(Request $request)
    {
        return $this->eventos->all();
    }

    public function listarConvidados($id)
    {
        return $this->eventos->convidados($id);
    }

    public function listarConvites()
    {
        return $this->eventos->convites();
    }

    public function statusConvite($id, $tipo)
    {
        $retorno =  $this->eventos->status($id,$tipo);

        if($retorno->original['success'] == true)
        {
            if($tipo == 1)
                return redirect()->back()->with('success', 'Convite Aceito com sucesso!');
            return redirect()->back()->with('success', 'Convite Cancelado com sucesso!');
        }
        return redirect()->back()->with('error', 'Não foi possível alterar o status do Convite!');
    }

    public function delete($id)
    {
        $retorno = $this->eventos->delete($id);
        if($retorno->original['success'] == true)
        {
            return redirect()->back()->with('success', 'Registro deletado com sucesso!');
        }
        return redirect()->back()->with('error', 'Não foi possível deletar o registro, verifique as dependências!');
        
    }

}
