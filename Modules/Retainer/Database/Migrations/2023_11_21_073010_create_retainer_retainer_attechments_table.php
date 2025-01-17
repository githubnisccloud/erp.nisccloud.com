<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('retainer_attechments')) {
            Schema::create('retainer_attechments', function (Blueprint $table) {
                $table->id();
                $table->string('retainer_id');
                $table->string('file_name');
                $table->string('file_path');
                $table->string('file_size');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retainer_retainer_attechments');
    }
};
