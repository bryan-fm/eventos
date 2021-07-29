<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTablesEvento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descricao');
            $table->date('data');
            $table->boolean('ativo')->default(1);
            $table->unsignedInteger('organizador_id');
            $table->timestamps();

            $table->foreign('organizador_id')->references('id')->on('users');
        });

        Schema::create('evento_x_usuario', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('evento_id');
            $table->unsignedInteger('usuario_id');
            $table->boolean('confirmacao')->default(1);
            $table->timestamps();

            $table->foreign('evento_id')->references('id')->on('eventos');
            $table->foreign('usuario_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('evento_x_usuario');
        Schema::drop('eventos');
    }
}
