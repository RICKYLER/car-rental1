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
        Schema::create('remote_commands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('command_type', ['lock', 'unlock', 'immobilize']);
            $table->string('justification', 255)->nullable();
            $table->string('requested_ip', 45)->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->enum('result_status', ['executed', 'rejected', 'expired'])->default('executed');
            $table->string('signature')->nullable();
            $table->string('payload_checksum')->nullable();
            $table->string('failure_reason')->nullable();
            $table->string('previous_hash')->nullable();
            $table->string('log_hash')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_commands');
    }
};
