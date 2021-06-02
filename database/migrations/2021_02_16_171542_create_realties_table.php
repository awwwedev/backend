<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRealtiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realties', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->text('img_path');
            $table->string('name');
            $table->float('area');
            $table->integer('price');
            $table->integer('discount_sum')->default(0);
            $table->integer('price_per_metr');
            $table->text('latitude');
            $table->text('longitude');
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->json('photo');
            $table->unsignedBigInteger('type_id');
            $table->foreign('type_id')->references('id')->on('realty_types')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('realties');
    }
}
