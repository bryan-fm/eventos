<?php

namespace App\Repositories;

use App\Models\Evento;
use App\Models\Evento_x_usuario;
use Illuminate\Support\Facades\Auth;
use DB;

class EventoRepository implements BaseRepository
{
    public function all(){
        return DB::table('eventos AS e')
        ->join('users', 'users.id', '=', 'e.organizador_id')
        ->selectRaw("e.id, users.name as organizador, e.descricao, e.data")
        ->where('users.id', '=', Auth::id())
        ->get();
    }

    public function get($id){
        return "teste";
    }

    public function store(array $data){
        $evento = (object)$data;
        $convidados = json_decode($evento->convidados);
        try
        {
            DB::beginTransaction();
            $evento = Evento::create($data);
            $id = $evento->id;

            foreach($convidados as $conv)
            {
                $convite = new Evento_x_usuario();

                $convite->evento_id = $id;
                $convite->usuario_id = $conv->id;
                $convite->confirmacao = 0;

                $convite->save();
            }
        }
        catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);

        }

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Registro Cadastrado com Sucesso!']);
    }

    public function update($id, array $data){
        return "teste";
    }

    public function delete($id){
        return "teste";
    }
}