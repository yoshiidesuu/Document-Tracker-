<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', function (Request $request) {
        return $request->user();
    })->name('api.user.show');

    Route::post('tokens/create', function (Request $request) {
        $request->validate(['name' => 'required|string|max:255']);
        $token = $request->user()->createToken($request->input('name'));

        return response()->json([
            'token' => $token->plainTextToken,
            'message' => 'Token created successfully.',
        ]);
    })->name('api.tokens.create');

    Route::delete('tokens/{id}', function (Request $request, $id) {
        $request->user()->tokens()->where('id', $id)->delete();

        return response()->json(['message' => 'Token revoked.']);
    })->name('api.tokens.revoke');

    // Chat routes
    Route::prefix('chat')->name('api.chat.')->group(function () {
        Route::get('conversations', [ChatController::class, 'conversations'])->name('conversations');
        Route::get('unread-count', [ChatController::class, 'unreadCount'])->name('unread');
        Route::get('users/search', [ChatController::class, 'searchUsers'])->name('users.search');
        Route::get('{user}', [ChatController::class, 'messages'])->name('messages');
        Route::get('{user}/poll', [ChatController::class, 'poll'])->name('poll');
        Route::post('{user}', [ChatController::class, 'send'])->name('send');
    });
});
