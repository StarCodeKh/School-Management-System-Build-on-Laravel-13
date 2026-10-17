<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender', 20);
            $table->date('date_of_birth');
            $table->string('roll', 50);
            $table->string('blood_group', 10);
            $table->string('religion', 50);
            $table->string('email')->unique();
            $table->string('class', 50);
            $table->string('section', 50);
            $table->string('admission_id', 50)->unique();
            $table->string('phone_number', 20);
            $table->string('upload')->nullable(); // photo path
            $table->timestamps();

            $table->index(['class', 'section']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
