<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string("product_id");
            $table->string('name');
            $table->timestamps();

            // In the migration file for galleries table
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('galleries');
    }
};
