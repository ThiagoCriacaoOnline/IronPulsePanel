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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('protocol_number', 30)->unique();
            $table->enum('requester_type', ["professional","student"]);
            $table->unsignedBigInteger('requester_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('related_professional_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('related_student_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('category', ["financial_dispute","verification_issue","abuse_report","technical_bug","lgpd_request"]);
            $table->enum('priority', ["low","medium","high","urgent"])->default('medium');
            $table->enum('status', ["open","in_progress","waiting_user","resolved","closed"])->default('open');
            $table->string('subject', 150);
            $table->text('description');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('resolution_notes')->nullable();
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
        Schema::dropIfExists('support_tickets');
    }
};
