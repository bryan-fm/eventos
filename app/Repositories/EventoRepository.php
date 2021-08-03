<?php

namespace App\Repositories;

use App\Models\Evento;
use App\Models\Evento_x_usuario;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use Mail;
use App\Mail\SendMailUser;

class EventoRepository implements BaseRepository
{
    public function all(){
        return DB::table('eventos AS e')
        ->join('users', 'users.id', '=', 'e.organizador_id')
        ->selectRaw("e.id, users.name as organizador, e.descricao, DATE_FORMAT(e.data,'%d/%m/%Y') as data")
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

    public function convites(){
        return DB::table('evento_x_usuario as e')
        ->join('eventos', 'eventos.id', '=', 'e.evento_id')
        ->join('users', 'users.id', '=', 'eventos.organizador_id')
        ->selectRaw("eventos.id, eventos.descricao, DATE_FORMAT(eventos.data,'%d/%m/%Y') as data, users.name as organizador, 
        case when e.confirmacao = 1 then 'Sim' else 'Não' end as status")
        ->where('e.usuario_id', '=', Auth::id())
        ->get();
    }

    public function get($id){
        return Evento::find($id);
    }

    public function status($id, $tipo){
        try
        {
            DB::beginTransaction();

            $obj = Evento_x_usuario::where('evento_id', $id)->where('usuario_id',Auth::id())->first();
            $obj->confirmacao = 0;
            if($tipo == 1)
                $obj->confirmacao = 1;
            $obj->save();
        }
        catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);

        }

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Status Alterado com Sucesso!']);
    }

    public function store(array $data, $id = 0){
        $evento = (object)$data;
        $convidados = json_decode($evento->convidados);
        $data_evento = Carbon::createFromFormat('d/m/Y', $evento->data);
        try
        {
            DB::beginTransaction();
            if($id == 0)
            {
                $obj = new Evento();
            }
            else
            {
                $obj = Evento::find($id);
            }

            $obj->descricao = $evento->descricao;
            $obj->data = $data_evento->format('Y-m-d');
            $obj->organizador_id = $evento->organizador_id;
            $obj->ativo = 1;
            $obj->save();
            
            $evento_id = $obj->id;

            if($evento_id != 0)
            {
                $query = DB::table('evento_x_usuario')
                ->selectRaw("id")->where("evento_id","=",$evento_id)
                ->whereNotIn('id',array_column($convidados, 'recid'))->delete();
            }

            foreach($convidados as $conv)
            {
                $query = Evento_x_usuario::where('evento_id',$evento_id)->where('usuario_id',$conv->usuario_id)->where('id','<>',$conv->recid)->get();
                if(count($query) > 0)
                    return response()->json(['success' => false, 'message' => 'Um usuário só pode ser cadastrado uma vez no evento, verifique os registros, Usuario: '. $conv->nome]);

                $convite = new Evento_x_usuario();

                if(Evento_x_usuario::find($conv->recid))
                    $convite = Evento_x_usuario::find($conv->recid);
                
                $convite->evento_id = $evento_id;
                $convite->usuario_id = $conv->usuario_id;
                $convite->confirmacao = 0;

                $convite->save();
            }
        }

        catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);

        }

        foreach($convidados as $conv)
        {
            try{
                Mail::to($conv->email)->queue(new SendMailUser());
    
            }catch (\Exception $exception){
                return response()->json(['message'=>'Erro ao enviar email'. $exception->getMessage(), 'success' => false]);
            }
        }
        

        DB::commit();

        if($id == 0)
        {
            return response()->json(['success' => true, 'message' => 'Registro Cadastrado com Sucesso!']);
        }
        return response()->json(['success' => true, 'message' => 'Registro Atualizado com Sucesso!']);
    }


    public function delete($id){
        
        try
        {
            DB::beginTransaction();
            Evento_x_usuario::where('evento_id',$id)->delete();
            Evento::find($id)->delete();
        }
        
        catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);

        }

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Registro Deletado com Sucesso!']);

    }
}