<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('cpf')->unique();
            $table->string('name');
            $table->string('password');
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->integer('id_owner');
            $table->string('name');
        });

        Schema::create('unitpeoples', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('name');
            $table->date('birthdate');
        });

        Schema::create('unitvehicles', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('plate');
            $table->string('color');
        });

        Schema::create('unitpets', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('name');
            $table->string('race');
        });

        Schema::create('walls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('body');
            $table->dateTime('date_created');
        });

        Schema::create('walllikes', function (Blueprint $table) {
            $table->id();
            $table->integer('id_wall');
            $table->integer('id_user');
        });

        Schema::create('docs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_url');
        });

        Schema::create('billets', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('file_url');
        });


        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('status')->default('in_review');
            $table->string('title');
            $table->dateTime('date_created');
            $table->string('photos');
        });

        Schema::create('foundandlost', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->string('status')->default('lost');
            $table->date('date_created');
            $table->string('where');
            $table->string('photo');
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->tinyInteger('allowed')->default(1);
            $table->string('cover');
            $table->string('days');
            $table->time('start_time');
            $table->time('end_time');
        });

        Schema::create('areadisableddays', function (Blueprint $table) {
            $table->id();
            $table->integer('id_area');
            $table->date('day');
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('id_area');
            $table->integer('id_unit');
            $table->dateTime('reservation_date');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('unit');
        Schema::dropIfExists('unitpeoples');
        Schema::dropIfExists('unitvehicles');
        Schema::dropIfExists('unitpets');
        Schema::dropIfExists('walls');
        Schema::dropIfExists('walllikes');
        Schema::dropIfExists('docs');
        Schema::dropIfExists('billets');
        Schema::dropIfExists('warnings');
        Schema::dropIfExists('foundandlost');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('areadisableddays');
        Schema::dropIfExists('reservations');
    }
}
