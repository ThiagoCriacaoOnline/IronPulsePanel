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
            $table->string('name', 150);
            $table->string('email', 150)->unique()->nullable();
            $table->string('password', 255)->nullable();
            $table->string('cpf', 14)->unique()->nullable();
            $table->string('phone', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('invite_activated_at')->nullable();
            $table->string('avatar', 255)->nullable();
            $table->string('path_avatar', 255)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ["male","female","other","prefer_not_to_say"])->nullable();
            $table->enum('goal', ["hypertrophy","weight_loss","conditioning","health","rehabilitation","sport_performance"])->nullable();
            $table->decimal('average_rating', 3, 2)->default(5.00);
            $table->integer('ratings_count')->default(0);
            $table->enum('registration_source', ["app_self_signup","invite_code","backoffice"])->default('app_self_signup');
            $table->foreignId('registered_by_professional_id')->nullable()->constrained('professional_profiles')->onDelete('set null');
            $table->timestamp('terms_accepted_at')->nullable();
            $table->string('terms_version', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('blocked_reason')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->foreignId('blocked_by')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('students');
    }
};
