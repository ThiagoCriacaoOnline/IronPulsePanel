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
        Schema::create('professional_payout_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')->unique()->constrained('professional_profiles')->onDelete('cascade');
            $table->enum('payout_mode', ["direct_pix","platform_intermediation"])->default('platform_intermediation');
            $table->enum('pix_key_type', ["cpf","cnpj","email","phone","random"])->nullable();
            $table->string('pix_key', 150)->nullable();
            $table->string('pix_holder_name', 150)->nullable();
            $table->string('pix_holder_document', 20)->nullable();
            $table->decimal('custom_fee_percentage', 5, 2)->nullable();
            $table->timestamp('fee_terms_accepted_at')->nullable();
            $table->string('fee_terms_version', 20)->nullable();
            $table->boolean('is_payout_blocked')->default(false);
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
        Schema::dropIfExists('professional_payout_settings');
    }
};
