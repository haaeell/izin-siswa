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

        Schema::create('dormitories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        /* SISWA */
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->string('name');
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('dormitory_id')->nullable()->constrained('dormitories')->nullOnDelete();
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
            $table->string('surat_walas')->nullable();
            $table->string('surat_ortu')->nullable();
            $table->string('surat_dokter')->nullable();

            $table->text('reject_reason')->nullable();

            $table->string('qr_token')->nullable()->unique();
            $table->timestamps();
        });

        /* CHECK-IN ASRAMA */
        Schema::create('student_permission_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_permission_id')->constrained()->cascadeOnDelete();
            $table->dateTime('checkin_at')->nullable();
            $table->dateTime('checkout_at')->nullable();
            $table->enum('status', ['TEPAT WAKTU', 'TERLAMBAT', 'DI LUAR']);
            $table->timestamps();
        });

        Schema::create('student_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['ringan', 'sedang', 'berat'])->nullable();
            $table->enum('handling_type', ['pengasuhan', 'pengajaran', 'pelatihan'])->nullable();
            $table->text('description')->nullable();
            $table->boolean('no_phone')->default(false);
            $table->boolean('no_permission')->default(false);
            $table->date('no_phone_until')->nullable();
            $table->date('no_permission_until')->nullable();
            $table->date('occurred_at')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users');
            $table->unsignedTinyInteger('attendance_percentage')->nullable();
            $table->date('attendance_until')->nullable();

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
