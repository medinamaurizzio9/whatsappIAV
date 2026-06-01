<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_account_id')->nullable();
            $table->string('phone_number_id')->nullable();
            $table->string('display_phone_number')->nullable();
            $table->text('access_token_encrypted')->nullable();
            $table->string('access_token_last_four', 10)->nullable();
            $table->string('verify_token');
            $table->text('app_secret_encrypted')->nullable();
            $table->string('webhook_url')->nullable();
            $table->string('api_version', 20)->default('v21.0');
            $table->string('attention_mode')->default('manual')->index();
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('whatsapp_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('wa_id')->unique();
            $table->string('phone', 60)->index();
            $table->string('name')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->json('profile_json')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_contact_id')->constrained('whatsapp_contacts')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('derivation_area_id')->nullable()->constrained('derivation_areas')->nullOnDelete();
            $table->string('status')->default('open')->index();
            $table->string('attention_mode')->default('manual')->index();
            $table->string('last_message_preview')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_conversation_id')->constrained('whatsapp_conversations')->cascadeOnDelete();
            $table->string('direction')->index();
            $table->string('message_id')->nullable()->index();
            $table->string('type')->default('text')->index();
            $table->text('body')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('intention_id')->nullable()->constrained('intentions')->nullOnDelete();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->string('recommended_action')->nullable();
            $table->foreignId('derivation_area_id')->nullable()->constrained('derivation_areas')->nullOnDelete();
            $table->boolean('requires_approval')->default(false);
            $table->json('payload_json')->nullable();
            $table->json('ai_result_json')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method', 10);
            $table->string('event_type')->nullable();
            $table->json('payload_json')->nullable();
            $table->string('signature')->nullable();
            $table->boolean('is_valid')->default(true);
            $table->string('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_media_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_message_id')->nullable()->constrained('whatsapp_messages')->nullOnDelete();
            $table->string('media_id')->index();
            $table->string('type')->index();
            $table->string('mime_type')->nullable();
            $table->string('filename')->nullable();
            $table->string('sha256')->nullable();
            $table->string('storage_path')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->json('payload_json')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_outbound_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_conversation_id')->nullable()->constrained('whatsapp_conversations')->nullOnDelete();
            $table->string('to_phone', 60)->index();
            $table->string('type')->default('text')->index();
            $table->text('body')->nullable();
            $table->boolean('success')->default(false);
            $table->string('error_message')->nullable();
            $table->json('request_json')->nullable();
            $table->json('response_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_outbound_logs');
        Schema::dropIfExists('whatsapp_media_files');
        Schema::dropIfExists('whatsapp_webhook_logs');
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_conversations');
        Schema::dropIfExists('whatsapp_contacts');
        Schema::dropIfExists('whatsapp_settings');
    }
};
