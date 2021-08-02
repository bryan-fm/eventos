@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Cadastro de Evento</div> 
                    <div class="card-body">

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Descrição</label> 
                            <div class="col-md-6">
                                <input id="descricao" required="required" autofocus="autofocus" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Data do Evento</label> 
                            <div class="col-md-6">
                                <input id="data" required="required" autofocus="autofocus" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                        <label for="select_convidado" class="col-md-4 col-form-label text-md-right">Convidado</label>
                            <div class="form-group col-md-4">
                                <select id="select_convidado" class="form-control">
                                    @foreach ($convidados as $conv)
                                        <option value="{{$conv->id}}" email = "{{$conv->email}}">{{$conv->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <button id="btn-add-convidado"  class="btn btn-primary">
                                    <i class="fa fa-btn fa-plus"></i>
                                    Adicionar
                                </button>
                            </div>
                        </div>
                        <div id="convidadosGrid" style="height: 450px"></div>

                        <button id="btn-add-edit"  class="btn btn-primary float-right" style="margin-top: 40px;">
                            <i class="fa fa-btn fa-envelope"></i>
                            Salvar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(function($) {
        $('#convidadosGrid').w2grid({
            name   : 'convidadosGrid',
            msgRefresh: 'Atualizando...',
            recid:'id',
            columns: [
                { field: 'id', text: 'Id', size: '30%' },
                { field: 'nome', text: 'Nome', size: '30%' },
                { field: 'email', text: 'Email', size: '40%' },
            ],
            
        });
        //Verificar Bug de Nao mostrar os objetos do grid
        $('#btn-add-convidado').on('click', function(){
            nome = $('#select_convidado option:selected').text();
            id = $('#select_convidado').val();
            email = $('#select_convidado option:selected').attr('email');
            w2ui['convidadosGrid'].add({ id: id, nome: nome, email:email});
        });


        @if($action == 'edit' || $action == 'view')
        {
            $('#descricao').val('{{$evento->descricao}}');
            $('#data').val('{{$evento->data}}');

        }
        @endif


        $('#btn-add-edit').on('click', function(){

            descricao = $('#descricao').val();
            data = $('#data').val();
            convidados = JSON.stringify(w2ui['convidadosGrid'].records);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                method: "POST",
                url: "{{route('add_evento')}}", 
                data:{
                    descricao,data,convidados
                },
            success: function(resposta){

                if (resposta.success){
                    alert(resposta.message, true);
                    window.location.href = '/';
                }else{
                    alert(JSON.stringify(resposta.errors));
                }
            },
            error: function(error)
            {
                alert(error)
            }
            });
        });

    });

</script>
@endsection
