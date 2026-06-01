<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AiInteractionController;
use App\Http\Controllers\AiPromptTemplateController;
use App\Http\Controllers\AiProviderSettingController;
use App\Http\Controllers\AiSandboxController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommercialDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DerivationAreaController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\InitialMenuOptionController;
use App\Http\Controllers\InternalChatController;
use App\Http\Controllers\IntentionController;
use App\Http\Controllers\KnowledgeCategoryController;
use App\Http\Controllers\KnowledgeDocumentController;
use App\Http\Controllers\KnowledgeFaqController;
use App\Http\Controllers\KnowledgeSearchController;
use App\Http\Controllers\KnowledgeIndexController;
use App\Http\Controllers\KnowledgeFeedbackController;
use App\Http\Controllers\UnansweredQuestionController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\RaffleController;
use App\Http\Controllers\SimulatedConversationController;
use App\Http\Controllers\WhatsAppInboxController;
use App\Http\Controllers\WhatsAppSettingsController;
use App\Http\Controllers\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/webhook/whatsapp', [WhatsAppWebhookController::class, 'verify'])->name('whatsapp.webhook.verify');
Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'receive'])->middleware('throttle:120,1')->name('whatsapp.webhook.receive');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('clients', ClientController::class);
    Route::get('/chat-interno', [InternalChatController::class, 'index'])->name('chat.index');
    Route::post('/chat-interno', [InternalChatController::class, 'store'])->name('chat.store');

    Route::middleware('admin')->group(function () {
        Route::get('/configuracion', [GeneralSettingController::class, 'edit'])->name('settings.edit');
        Route::put('/configuracion', [GeneralSettingController::class, 'update'])->name('settings.update');
        Route::resource('menu-options', InitialMenuOptionController::class)->parameters(['menu-options' => 'menuOption']);
        Route::resource('derivation-areas', DerivationAreaController::class)->parameters(['derivation-areas' => 'derivationArea']);
        Route::get('/conversaciones-simuladas', [SimulatedConversationController::class, 'index'])->name('conversations.index');
        Route::get('/conversaciones-simuladas/{conversation}', [SimulatedConversationController::class, 'show'])->name('conversations.show');
        Route::resource('knowledge-categories', KnowledgeCategoryController::class)->parameters(['knowledge-categories' => 'knowledgeCategory']);
        Route::resource('knowledge-documents', KnowledgeDocumentController::class)->parameters(['knowledge-documents' => 'knowledgeDocument']);
        Route::resource('knowledge-faqs', KnowledgeFaqController::class)->parameters(['knowledge-faqs' => 'knowledgeFaq']);
        Route::resource('products', ProductController::class);
        Route::resource('promotions', PromotionController::class);
        Route::resource('raffles', RaffleController::class);
        Route::resource('intentions', IntentionController::class);
        Route::resource('ai-providers', AiProviderSettingController::class)->parameters(['ai-providers' => 'aiProvider']);
        Route::resource('ai-prompts', AiPromptTemplateController::class)->parameters(['ai-prompts' => 'aiPrompt']);
        Route::get('/ia/historial', [AiInteractionController::class, 'index'])->name('ai-interactions.index');
        Route::get('/ia/historial/{interaction}', [AiInteractionController::class, 'show'])->name('ai-interactions.show');
        Route::post('/knowledge/reindex', [KnowledgeIndexController::class, 'store'])->name('knowledge.reindex');
        Route::resource('knowledge-feedback', KnowledgeFeedbackController::class)->only(['index', 'store']);
        Route::resource('unanswered-questions', UnansweredQuestionController::class)->except(['create', 'store']);
        Route::get('/buscador-ia', [KnowledgeSearchController::class, 'index'])->name('knowledge-search.index');
        Route::post('/buscador-ia', [KnowledgeSearchController::class, 'search'])->name('knowledge-search.search');
        Route::get('/whatsapp/settings', [WhatsAppSettingsController::class, 'edit'])->name('whatsapp.settings');
        Route::put('/whatsapp/settings', [WhatsAppSettingsController::class, 'update'])->name('whatsapp.settings.update');
    });

    Route::get('/whatsapp/inbox', [WhatsAppInboxController::class, 'index'])->name('whatsapp.inbox');
    Route::get('/whatsapp/conversations/{conversation}', [WhatsAppInboxController::class, 'show'])->name('whatsapp.conversations.show');
    Route::put('/whatsapp/conversations/{conversation}', [WhatsAppInboxController::class, 'update'])->name('whatsapp.conversations.update');
    Route::post('/whatsapp/conversations/{conversation}/reply', [WhatsAppInboxController::class, 'reply'])->name('whatsapp.conversations.reply');
    Route::post('/whatsapp/messages/{message}/approve', [WhatsAppInboxController::class, 'approve'])->name('whatsapp.messages.approve');
    Route::get('/ia/sandbox', [AiSandboxController::class, 'index'])->name('ai-sandbox.index');
    Route::post('/ia/sandbox', [AiSandboxController::class, 'run'])->name('ai-sandbox.run');
    Route::get('/comercial', CommercialDashboardController::class)->name('commercial.dashboard');
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/{client}', [LeadController::class, 'show'])->name('leads.show');
    Route::put('/leads/{client}/stage', [LeadController::class, 'updateStage'])->name('leads.stage');
    Route::get('/reportes/leads', [LeadReportController::class, 'index'])->name('lead-reports.index');
});
