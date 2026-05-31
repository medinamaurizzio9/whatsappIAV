<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientController;
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
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\RaffleController;
use App\Http\Controllers\SimulatedConversationController;
use Illuminate\Support\Facades\Route;

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
        Route::get('/buscador-ia', [KnowledgeSearchController::class, 'index'])->name('knowledge-search.index');
        Route::post('/buscador-ia', [KnowledgeSearchController::class, 'search'])->name('knowledge-search.search');
    });
});
