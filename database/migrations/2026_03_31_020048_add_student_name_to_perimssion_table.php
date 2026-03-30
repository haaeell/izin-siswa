<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_permissions', function (Blueprint $table) {
            $table->string('student_name')->nullable()->after('student_id');
            $table->string('student_class')->nullable()->after('student_name');
        });
    }

    public function down(): void
    {
        Schema::table('student_permissions', function (Blueprint $table) {
            $table->dropColumn(['student_name', 'student_class']);
        });
    }
};
