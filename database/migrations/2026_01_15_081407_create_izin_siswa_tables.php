<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        /* role: wali_kelas | perizinan */
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('wali_kelas');
        });

        /* TAHUN AKADEMIK */
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        /* KELAS */
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('academic_year_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('wali_kelas_id')->constrained('users');
            $table->timestamps();
        });

        /* SISWA */
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->string('name');
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->timestamps();
        });

        /* PERIZINAN SISWA */
        Schema::create('student_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wali_kelas_id')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');

            $table->string('type');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->text('reason');

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reject_reason')->nullable();

            $table->string('qr_token')->nullable()->unique();
            $table->timestamps();
        });

        /* CHECK-IN ASRAMA */
        Schema::create('student_permission_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_permission_id')->constrained()->cascadeOnDelete();
            $table->dateTime('checkin_at');
            $table->enum('status', ['TEPAT WAKTU', 'TERLAMBAT']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_permission_checkins');
        Schema::dropIfExists('student_permissions');
        Schema::dropIfExists('students');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('academic_years');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
