<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('slot_id');
            $table->foreign('slot_id')->references('id')->on('availability_slots')->onDelete('cascade');
            $table->unsignedInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->text('medical_note')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('appointments'); }
};
