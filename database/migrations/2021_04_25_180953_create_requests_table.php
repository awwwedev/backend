<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->text('message')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('new')->default(true);
            $table->unsignedBigInteger('realtie_id')->nullable();
            $table->foreign('realtie_id')->references('id')->on('realties')->nullOnDelete();
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
        Schema::dropIfExists('requests');
    }
}
