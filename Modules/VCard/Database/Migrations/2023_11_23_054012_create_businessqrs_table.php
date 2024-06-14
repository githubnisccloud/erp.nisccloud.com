<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('businessqrs')) {
            Schema::create('businessqrs', function (Blueprint $table) {
                $table->id();
                $table->integer('business_id')->nullable();
                $table->string('foreground_color')->nullable();
                $table->string('background_color')->nullable();
                $table->string('radius')->nullable();
                $table->string('qr_type')->nullable();
                $table->string('qr_text')->nullable();
                $table->string('qr_text_color')->nullable();
                $table->string('image')->nullable();
                $table->string('size')->nullable();
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
        Schema::dropIfExists('businessqrs');
    }
};
