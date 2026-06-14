<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->boolean('is_accepted')->default(false);
            $table->decimal('avg_rating', 3, 2)->default(0.00);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('doctor_profiles'); }
};
