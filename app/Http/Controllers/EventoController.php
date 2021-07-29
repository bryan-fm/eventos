<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;

class EventoController extends Controller
{

    public function AddView()
    {
        return view('EventoForm');
    }

    public function insert(Request $request)
    {
        $data = json_decode($request->convidados);
        try
        {
            DB::beginTransaction();
            $obj = new Evento();

            $obj->descricao  = $request->descricao;
            $obj->data  = $request->data;
            $obj->ativo  = 1;
            $obj->organizador_id  = Auth::id();
            

            $obj->save();
        } 
        catch (\Exception $e)
        {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Registros Cadastrados com Sucesso!']); 
    }

    public function listar(Request $request)
    {
        $query = DB::table('eventos AS e')
            ->join('users', 'users.id', '=', 'e.organizador_id')
            ->selectRaw("e.id, users.name as organizador, e.descricao, e.data")
            ->where('users.id', '=', Auth::id())
            ->get();

        return $query;
    }
}
