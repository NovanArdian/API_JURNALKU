<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->string('nama');
            $table->string('rombel');
            $table->string('rayon');
            $table->string('password');
            $table->string('medsos')->nullable();
            $table->string('portofolio')->nullable();
            $table->string('certifikat')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('siswas');
    }
};