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
        Schema::create('student_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['ringan', 'sedang', 'berat']);
            $table->text('description');
            $table->boolean('no_phone')->default(false);
            $table->boolean('no_permission')->default(false);
            $table->date('until');
            $table->date('occurred_at'); //tanggal kejadian pelanggaran
            $table->foreignId('reported_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_violations');
    }
};
