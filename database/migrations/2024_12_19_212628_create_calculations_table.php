<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalculationsTable extends Migration
{
    public function up()
    {
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->string('expression');  
            $table->decimal('result', 65, 8);
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('calculations');
    }
}
