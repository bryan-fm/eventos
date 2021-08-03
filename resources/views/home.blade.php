@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Eventos</div>
                @guest
                    <h1>Faça Login para verificar os Eventos</h1>
                @else
                @if (\Session::has('success'))
                    <div class="alert alert-success">
                        <ul>
                            <li>{!! \Session::get('success') !!}</li>
                        </ul>
                    </div>
                @endif
                @if (\Session::has('error'))
                    <div class="alert alert-error">
                        <ul>
                            <li>{!! \Session::get('error') !!}</li>
                        </ul>
                    </div>
                @endif
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab1" role="tab" id="tb1">Meus Eventos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab2" role="tab" id="tb2">Convites</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="show active tab-pane fade in" id="tab1">
                            <div id="myGrid" style="height: 450px"></div>
                        </div>
                        <div class="tab-pane fade in" id="tab2">
                            <div id="convitesGrid" style="height: 450px"></div>
                        </div>
                    </div>
                @endguest
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(function($) {

    $().w2destroy("#myGrid");
    $('#myGrid').w2grid({
        name   : 'myGrid',
        url    : '{{route('listar_evento')}}',
        name: 'eventosGrid',
        header: 'Lista de Eventos',
        msgRefresh: 'Atualizando...',
        recid:'id',
        show: {
                footer: true,
                toolbar: true,
                toolbarAdd: true,
                toolbarDelete: false,
                toolbarEdit: true,
                header: true,
                toolbarColumns: false,
                searchAll: false,
                toolbarInput: false
            },
        columns: [
            { field: 'id', text: 'Id', size: '10%' },
            { field: 'organizador', text: 'Organizador', size: '30%' },
            { field: 'descricao', text: 'Descricao', size: '40%' },
            { field: 'data', text: 'Data', size: '20%' },
        ],
        toolbar: 
        {
            id: toolbar,
            items: [
                { type: 'button', id: 'btn-del', caption: 'Deletar', icon: 'fa fa-times', disabled: true},
            ],
            onClick: function (target, data) {
                if (target == 'btn-del'){
                    var id = w2ui[this.owner.name].getSelection().toString();
                    window.location.href = '/deletarEvento/' + id;
                }
            }
        },
        onAdd: function (event) {
            window.location.href = '/cadastrarEvento';
        },
        onEdit: function (event) {
            var id = event.recid.toString();
            window.location.href = '/editarEvento/' + id;
        },
        onSelect: function(event) {
            this.toolbar.enable("btn-del");
        },
        onUnselect: function(event) {
            this.toolbar.disable("btn-del");
        },
        
    });
    
    $().w2destroy("#convitesGrid");
    $('#convitesGrid').w2grid({
        name   : 'convitesGrid',
        url    : '{{route('listar_convites')}}',
        name: 'convitesGrid',
        header: 'Lista de Convites',
        msgRefresh: 'Atualizando...',
        recid:'id',
        show: {
                footer: true,
                toolbar: true,
                toolbarAdd: false,
                toolbarDelete: false,
                toolbarEdit: false,
                header: true,
                toolbarColumns: false,
                searchAll: false,
                toolbarInput: false
            },
        columns: [
            { field: 'id', text: 'Id', size: '10%' },
            { field: 'organizador', text: 'Organizador', size: '30%' },
            { field: 'descricao', text: 'Descricao', size: '40%' },
            { field: 'data', text: 'Data', size: '10%' },
            { field: 'status', text: 'Confirmou', size: '10%' },
        ],
        toolbar: 
        {
            id: toolbar,
            items: [
                { type: 'button', id: 'btn-aceitar', caption: 'Aceitar Convite', icon: 'fa fa-times', disabled: true},
                { type: 'button', id: 'btn-rejeitar', caption: 'Rejeitar Convite', icon: 'fa fa-times', disabled: true},
            ],
            onClick: function (target, data) {
                if (target == 'btn-aceitar'){
                    var id = w2ui[this.owner.name].getSelection().toString();
                    window.location.href = '/statusConvite/' + id + '/' + 1;
                }
                if (target == 'btn-rejeitar'){
                    var id = w2ui[this.owner.name].getSelection().toString();
                    window.location.href = '/statusConvite/' + id + '/' + 2;
                }
            }
        },
        onSelect: function(event) {
            this.toolbar.enable("btn-aceitar");
            this.toolbar.enable("btn-rejeitar");
        },
        onUnselect: function(event) {
            this.toolbar.disable("btn-aceitar");
            this.toolbar.enable("btn-rejeitar");
        },
        
    });

    $('#tb2').on('click',function(){
        w2ui["convitesGrid"].reload();
        w2ui["eventosGrid"].reload();
    });

    $('#tb1').on('click',function(){
        w2ui["convitesGrid"].reload();
        w2ui["eventosGrid"].reload();
    });

});
</script>

@endsection
