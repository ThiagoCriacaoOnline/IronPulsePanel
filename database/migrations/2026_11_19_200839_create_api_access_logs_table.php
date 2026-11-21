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
        Schema::create('api_access_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('portal', ["student_app","professional_panel","website"]);
            $table->enum('actor_type', ["student","professional","guest"])->default('guest');
            $table->unsignedBigInteger('actor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('method', 10);
            $table->string('endpoint', 255);
            $table->integer('status_code');
            $table->integer('response_time_ms');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_access_logs');
    }
};
