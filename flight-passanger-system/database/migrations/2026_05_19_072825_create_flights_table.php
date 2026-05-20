<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up(): void
{
    Schema::create('flights', function (Blueprint $table) {
        $table->id();
        $table->string('number')->unique();
        $table->string('departure_city');
        $table->string('arrival_city');
        $table->dateTime('departure_time');
        $table->dateTime('arrival_time');
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
        Schema::dropIfExists('flights');
    }
};
