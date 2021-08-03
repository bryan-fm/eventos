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
            autoLoad: false,
            recid:'id',
            show: {
                footer: true,
                toolbar: true,
                toolbarAdd: false,
                toolbarDelete: false,
                toolbarEdit: false,
                header: false,
                toolbarColumns: false,
                searchAll: false,
                toolbarInput: false,
                toolbarReload: false,
            },
            columns: [
                { field: 'id', text: 'Id', hidden: true },
                { field: 'usuario_id', text: 'Id Usuario', size: '30%' },
                { field: 'nome', text: 'Nome', size: '30%' },
                { field: 'email', text: 'Email', size: '40%' },
            ],
            toolbar: {
            id: toolbar,
            items: [
                { type: 'button', id: 'btn-del', caption: 'Deletar', icon: 'fa fa-times', disabled: true},
            ],
                onClick: function (target, data)   
                {
                    if (target == 'btn-del'){
                        var id = w2ui[this.owner.name].getSelection().toString();
                        w2ui['convidadosGrid'].remove(id);
                        $('#grid_convidadosGrid_rec_more').remove();
                    }
                }
            },
            onSelect: function(event) {
                this.toolbar.enable("btn-del");
            },
            onUnselect: function(event) {
                this.toolbar.disable("btn-del");
            },
            
        });
        $('#grid_convidadosGrid_rec_more').remove();

        $( "#data" ).datepicker({ dateFormat: 'dd/mm/yy' })

        $('#btn-add-convidado').on('click', function(){
            recid = 'NEW_' + Math.floor(100000 + Math.random() * 900000);
            nome = $('#select_convidado option:selected').text();
            usuario_id = $('#select_convidado').val();
            email = $('#select_convidado option:selected').attr('email');
            w2ui['convidadosGrid'].add({ recid: recid, nome: nome, usuario_id: usuario_id, email:email});
            $('#grid_convidadosGrid_rec_more').remove();
        });

        @if($action == 'edit')
        {
            data = "{{Carbon\Carbon::parse($evento->data)->format('d/m/Y')}}"
            $('#descricao').val('{{$evento->descricao}}');
            $('#data').val(data);
            w2ui['convidadosGrid'].url = '{{route('listar_convidados',['id' => $evento->id])}}';
            w2ui['convidadosGrid'].reload();
        }
        @endif


        $('#btn-add-edit').on('click', function(){

            descricao = $('#descricao').val();
            data = $('#data').val();
            convidados = JSON.stringify(w2ui['convidadosGrid'].records);

            @if ($action == 'add')

                url =  "{{route('add_edit_evento',['id' => 0])}}"

            @endif

             @if ($action == 'edit')

                url =  "{{route('add_edit_evento',['id' => $evento->id])}}"

            @endif

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                method: "POST",
                url: url, 
                data:{
                    descricao,data,convidados
                },
            success: function(resposta){

                if (resposta.success){
                    alert(resposta.message, true);
                    window.location.href = '/';
                }else{
                    alert(JSON.stringify(resposta));
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
