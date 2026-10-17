<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $now = now();
        DB::table('fee_types')->insert(array_map(fn (string $name) => [
            'name' => $name,
            'created_at' => $now,
            'updated_at' => $now,
        ], ['Class Test', 'Exam Fees', 'Hostel Fees', 'Transport Fees', 'Mess Fees']));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_types');
    }
};
