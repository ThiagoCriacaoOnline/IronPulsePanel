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
        Schema::create('professional_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->string('cpf_cnpj', 20)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('avatar', 255)->nullable();
            $table->string('path_avatar', 255)->nullable();
            $table->enum('profession_type', ["nutritionist","personal","both"]);
            $table->enum('council_type', ["crn","cref"]);
            $table->string('council_number', 20);
            $table->string('council_state', 2);
            $table->enum('verification_status', ["pending","approved","rejected"])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            $table->decimal('average_rating', 3, 2)->default(5.00);
            $table->integer('ratings_count')->default(0);
            $table->text('bio')->nullable();
            $table->boolean('is_accepting_students')->default(false);
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('professional_profiles');
    }
};
