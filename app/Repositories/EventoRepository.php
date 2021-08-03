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

    public function convidados($id){
        return DB::table('evento_x_usuario as e')
        ->join('users', 'users.id', '=', 'e.usuario_id')
        ->selectRaw("e.id, e.usuario_id, users.name as nome, users.email")
        ->where('e.evento_id', '=', $id)
        ->get();

    }

    public function get($id){
        return Evento::find($id);
    }

    public function store(array $data, $id = 0){
        $evento = (object)$data;
        $convidados = json_decode($evento->convidados);
        try
        {
            DB::beginTransaction();
            if($id == 0)
            {
                $evento = Evento::create($data);
            }
            else
            {
                $evento = Evento::find($id);
                $evento->update($data);
            }
            
            $id = $evento->id;

            if($id != 0)
            {
                $query = DB::table('evento_x_usuario')
                ->selectRaw("id")->where("evento_id","=",$id)
                ->whereNotIn('id',array_column($convidados, 'recid'))->delete();
            }

            foreach($convidados as $conv)
            {
                $query = Evento_x_usuario::where('evento_id',$id)->where('usuario_id',$conv->usuario_id)->where('id','<>',$conv->recid)->get();
                if(count($query) > 0)
                    return response()->json(['success' => false, 'message' => 'Um usuário só pode ser cadastrado uma vez no evento, verifique os registros, Usuario: '. $conv->nome]);

                $convite = new Evento_x_usuario();

                if(Evento_x_usuario::find($conv->recid))
                    $convite = Evento_x_usuario::find($conv->recid);
                
                $convite->evento_id = $id;
                $convite->usuario_id = $conv->usuario_id;
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