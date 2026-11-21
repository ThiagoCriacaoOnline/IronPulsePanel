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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_student_id')->constrained('professional_students')->onDelete('cascade');
            $table->enum('reviewer_type', ["student","professional"]);
            $table->unsignedBigInteger('reviewer_id')->index()->constrained('users')->onDelete('set null');
            $table->enum('reviewed_type', ["student","professional"]);
            $table->unsignedBigInteger('reviewed_id')->index()->constrained('users')->onDelete('set null');
            $table->unsignedTinyInteger('score');
            $table->text('comment')->nullable();
            $table->json('tags')->nullable();
            $table->enum('moderation_status', ["published","hidden_by_moderation","reported"])->default('published');
            $table->timestamp('moderated_at')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('moderation_reason')->nullable();
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
        Schema::dropIfExists('ratings');
    }
};
