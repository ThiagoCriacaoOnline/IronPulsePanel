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
        Schema::create('student_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')->constrained('professional_profiles')->onDelete('cascade');
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->enum('service_type', ["training","nutrition","both"])->default('training');
            $table->string('code', 20)->unique();
            $table->enum('invite_type', ["single_student","open_link"])->default('single_student');
            $table->integer('max_uses')->default(1);
            $table->integer('uses_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ["pending","used","expired","revoked"])->default('pending');
            $table->timestamp('used_at')->nullable();
            $table->string('used_ip', 45)->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->enum('revoked_by_type', ["professional","backoffice"])->nullable();
            $table->unsignedBigInteger('revoked_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('revoke_reason', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_invites');
    }
};
