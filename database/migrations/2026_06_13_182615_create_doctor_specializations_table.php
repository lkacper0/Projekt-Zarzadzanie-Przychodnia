<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('doctor_specializations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('doctor_id');
            $table->foreign('doctor_id')->references('id')->on('doctor_profiles')->onDelete('cascade');
            $table->unsignedInteger('specialization_id');
            $table->foreign('specialization_id')->references('id')->on('specializations')->onDelete('cascade');
        });
    }
    public function down(): void { Schema::dropIfExists('doctor_specializations'); }
};
