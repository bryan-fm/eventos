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
                    <div id="myGrid" style="height: 450px"></div>
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
                toolbarDelete: true,
                toolbarEdit: true,
                header: true,
                toolbarColumns: false,
                searchAll: false,
                toolbarInput: false
            },
        columns: [
            { field: 'id', text: 'Id', size: '30%' },
            { field: 'organizador', text: 'Organizador', size: '30%' },
            { field: 'descricao', text: 'Descricao', size: '40%' },
            { field: 'data', text: 'Data', size: '40%' },
        ],
        onAdd: function (event) {
            window.location.href = '/cadastrarEvento';
        },
        onEdit: function (event) {
            var id = event.recid.toString();
            window.location.href = '/editarEvento/' + id;
        },
        
    });
});
</script>

@endsection
