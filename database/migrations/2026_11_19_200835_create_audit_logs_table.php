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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('actor_type', ["backoffice_user","professional","student","system"]);
            $table->unsignedBigInteger('actor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('actor_name', 150)->nullable();
            $table->enum('portal', ["backoffice","professional_panel","student_app","website","api_webhook"]);
            $table->string('action', 50);
            $table->string('auditable_type', 100);
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('request_url')->nullable();
            $table->string('request_method', 10)->nullable();
            $table->enum('severity', ["info","warning","critical"])->default('info');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
