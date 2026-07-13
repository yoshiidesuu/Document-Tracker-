<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\System\ActivityLogController;
use App\Http\Controllers\System\ArtaSettingController;
use App\Http\Controllers\System\DepartmentController;
use App\Http\Controllers\System\DocumentController;
use App\Http\Controllers\System\DocumentTypeController;
use App\Http\Controllers\System\EmailSettingController;
use App\Http\Controllers\System\OfficeController;
use App\Http\Controllers\System\PermissionController;
use App\Http\Controllers\System\ProfileController;
use App\Http\Controllers\System\RoleController;
use App\Http\Controllers\System\SecurityLogController;
use App\Http\Controllers\System\SettingController;
use App\Http\Controllers\System\StatisticsController;
use App\Http\Controllers\System\UserController;
use App\Http\Controllers\SystemController;
use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('file/favicon', [FileController::class, 'favicon'])->name('favicon')->middleware('auth');
Route::get('file/settings/logo', [FileController::class, 'logo'])->name('file.logo')->middleware('auth');
Route::get('file/settings/favicon', [FileController::class, 'favicon'])->name('file.favicon')->middleware('auth');
Route::get('file/settings/document-logo-right', [FileController::class, 'documentRightLogo'])->name('file.document-logo-right')->middleware('auth');
Route::get('file/profile/{filename}', [FileController::class, 'profile'])->name('file.profile')->middleware('auth');

Route::get('check-credential', function (Request $request) {
    $q = $request->input('q');
    if (! $q || strlen($q) < 2) {
        return response()->json(['exists' => false]);
    }
    $field = filter_var($q, FILTER_VALIDATE_EMAIL) ? 'email' : 'id_number';
    $user = User::where($field, $q)->first(['id', 'firstname', 'lastname', 'email', 'id_number', 'profile_picture']);

    return response()->json([
        'exists' => (bool) $user,
        'field' => $user ? $field : null,
        'user' => $user ? [
            'name' => $user->full_name,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'profile_picture' => $user->profile_picture_url,
        ] : null,
    ]);
})->middleware(ThrottleRequests::with(30, 1))->name('check-credential');

Route::middleware('guest')->group(function () {
    Route::get('login', function () {
        return view('welcome');
    })->name('login.form');

    Route::get('auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

    Route::post('login', LoginController::class)
        ->middleware(
            ThrottleRequests::with(
                config('security.rate_limiting.login.max_attempts', 5),
                config('security.rate_limiting.login.decay_minutes', 1)
            )
        )
        ->name('login');

    Route::get('register', function () {
        return view('welcome', ['showRegisterMessage' => true]);
    })->name('register.form');

    Route::post('register', RegisterController::class)
        ->middleware(
            ThrottleRequests::with(
                config('security.rate_limiting.web.max_attempts', 60),
                config('security.rate_limiting.web.decay_minutes', 1)
            )
        )
        ->name('register');

    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
        ->name('password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.reset.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', LogoutController::class)->name('logout');
    Route::post('password/change', PasswordChangeController::class)->name('password.change');

    Route::get('user', function (Request $request) {
        return $request->user();
    })->name('user.show');

    Route::redirect('dashboard', '/system/');

    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/', [SystemController::class, 'dashboard'])->name('dashboard');

        Route::get('profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('settings', [SettingController::class, 'index'])->name('settings');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::post('/', [PermissionController::class, 'update'])->name('update');
            Route::post('toggle', [PermissionController::class, 'toggle'])->name('toggle');
        });

        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('{role}/edit', [RoleController::class, 'edit'])->name('edit')->whereNumber('role');
            Route::post('{role}', [RoleController::class, 'update'])->name('update')->whereNumber('role');
            Route::delete('{role}', [RoleController::class, 'destroy'])->name('destroy')->whereNumber('role');
        });

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::post('bulk', [UserController::class, 'bulkAction'])->name('bulk');
            Route::get('{user}', [UserController::class, 'view'])->name('view')->whereNumber('user');
            Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit')->whereNumber('user');
            Route::post('{user}', [UserController::class, 'update'])->name('update')->whereNumber('user');
            Route::post('{user}/password', [UserController::class, 'updatePassword'])->name('password')->whereNumber('user');
            Route::post('{user}/ban', [UserController::class, 'ban'])->name('ban')->whereNumber('user');
            Route::post('{user}/unban', [UserController::class, 'unban'])->name('unban')->whereNumber('user');
            Route::post('{user}/lock', [UserController::class, 'lock'])->name('lock')->whereNumber('user');
            Route::post('{user}/unlock', [UserController::class, 'unlock'])->name('unlock')->whereNumber('user');
            Route::post('{user}/force-logout', [UserController::class, 'forceLogout'])->name('force-logout')->whereNumber('user');
            Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy')->whereNumber('user');
        });

        Route::prefix('departments')->name('departments.')->group(function () {
            Route::get('/', [DepartmentController::class, 'index'])->name('index');
            Route::get('create', [DepartmentController::class, 'create'])->name('create');
            Route::post('/', [DepartmentController::class, 'store'])->name('store');
            Route::get('{department}', [DepartmentController::class, 'view'])->name('view')->whereNumber('department');
            Route::get('{department}/edit', [DepartmentController::class, 'edit'])->name('edit')->whereNumber('department');
            Route::post('{department}', [DepartmentController::class, 'update'])->name('update')->whereNumber('department');
            Route::post('{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('toggle-status')->whereNumber('department');
            Route::delete('{department}', [DepartmentController::class, 'destroy'])->name('destroy')->whereNumber('department');
        });

        Route::prefix('offices')->name('offices.')->group(function () {
            Route::get('/', [OfficeController::class, 'index'])->name('index');
            Route::get('create', [OfficeController::class, 'create'])->name('create');
            Route::post('/', [OfficeController::class, 'store'])->name('store');
            Route::get('{office}', [OfficeController::class, 'view'])->name('view')->whereNumber('office');
            Route::get('{office}/edit', [OfficeController::class, 'edit'])->name('edit')->whereNumber('office');
            Route::post('{office}', [OfficeController::class, 'update'])->name('update')->whereNumber('office');
            Route::post('{office}/toggle-status', [OfficeController::class, 'toggleStatus'])->name('toggle-status')->whereNumber('office');
            Route::delete('{office}', [OfficeController::class, 'destroy'])->name('destroy')->whereNumber('office');
        });

        Route::get('messages', [ChatController::class, 'index'])->name('messages');

        Route::get('email-settings', [EmailSettingController::class, 'index'])->name('email-settings');
        Route::post('email-settings', [EmailSettingController::class, 'update'])->name('email-settings.update');
        Route::post('email-settings/test', [EmailSettingController::class, 'test'])->name('email-settings.test');

        Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics');

        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [DocumentController::class, 'index'])->name('index');
            Route::get('create', [DocumentController::class, 'create'])->name('create');
            Route::post('/', [DocumentController::class, 'store'])->name('store');
            Route::get('{document}', [DocumentController::class, 'view'])->name('view')->whereNumber('document');
            Route::get('{document}/edit', [DocumentController::class, 'edit'])->name('edit')->whereNumber('document');
            Route::post('{document}', [DocumentController::class, 'update'])->name('update')->whereNumber('document');
            Route::delete('{document}', [DocumentController::class, 'destroy'])->name('destroy')->whereNumber('document');
            Route::get('{document}/print', [DocumentController::class, 'print'])->name('print')->whereNumber('document');
            Route::get('my', [DocumentController::class, 'myDocuments'])->name('my');
            Route::get('my-scanned', [DocumentController::class, 'myScanned'])->name('my-scanned');
            Route::get('receive', [DocumentController::class, 'receiveScanner'])->name('receive');
            Route::post('receive/lookup', [DocumentController::class, 'lookupByCode'])->name('receive.lookup');
            Route::post('receive/{document}', [DocumentController::class, 'receiveDocument'])->name('receive.store');
            Route::get('finish', [DocumentController::class, 'finishScanner'])->name('finish');
            Route::post('finish/lookup', [DocumentController::class, 'lookupByCode'])->name('finish.lookup');
            Route::post('finish/{document}', [DocumentController::class, 'finishDocument'])->name('finish.store');
            Route::get('terminate', [DocumentController::class, 'terminateScanner'])->name('terminate');
            Route::post('terminate/lookup', [DocumentController::class, 'lookupByCode'])->name('terminate.lookup');
            Route::post('terminate/{document}', [DocumentController::class, 'terminateDocument'])->name('terminate.store');
            Route::post('{document}/reopen', [DocumentController::class, 'reopenDocument'])->name('reopen');
        });

        Route::prefix('document-types')->name('document-types.')->group(function () {
            Route::get('/', [DocumentTypeController::class, 'index'])->name('index');
            Route::get('create', [DocumentTypeController::class, 'create'])->name('create');
            Route::post('/', [DocumentTypeController::class, 'store'])->name('store');
            Route::get('{documentType}', [DocumentTypeController::class, 'view'])->name('view')->whereNumber('documentType');
            Route::get('{documentType}/edit', [DocumentTypeController::class, 'edit'])->name('edit')->whereNumber('documentType');
            Route::post('{documentType}', [DocumentTypeController::class, 'update'])->name('update')->whereNumber('documentType');
            Route::post('{documentType}/toggle-status', [DocumentTypeController::class, 'toggleStatus'])->name('toggle-status')->whereNumber('documentType');
            Route::delete('{documentType}', [DocumentTypeController::class, 'destroy'])->name('destroy')->whereNumber('documentType');
        });

        Route::prefix('arta-settings')->name('arta-settings.')->group(function () {
            Route::get('/', [ArtaSettingController::class, 'index'])->name('index');
            Route::get('create', [ArtaSettingController::class, 'create'])->name('create');
            Route::post('/', [ArtaSettingController::class, 'store'])->name('store');
            Route::get('{artaSetting}', [ArtaSettingController::class, 'view'])->name('view')->whereNumber('artaSetting');
            Route::get('{artaSetting}/edit', [ArtaSettingController::class, 'edit'])->name('edit')->whereNumber('artaSetting');
            Route::post('{artaSetting}', [ArtaSettingController::class, 'update'])->name('update')->whereNumber('artaSetting');
            Route::delete('{artaSetting}', [ArtaSettingController::class, 'destroy'])->name('destroy')->whereNumber('artaSetting');
            Route::post('{artaSetting}/toggle-status', [ArtaSettingController::class, 'toggleStatus'])->name('toggle-status')->whereNumber('artaSetting');
        });

        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        });

        Route::prefix('security-logs')->name('security-logs.')->group(function () {
            Route::get('/', [SecurityLogController::class, 'index'])->name('index');
        });
    });
});

Route::get('user/verify-session', function (Request $request) {
    if (! auth()->check()) {
        return response()->json(['valid' => false]);
    }
    $fingerprint = sha1($request->ip().'|'.$request->userAgent());
    $stored = session('security.fingerprint');
    $valid = $stored && hash_equals($stored, $fingerprint);
    if (! $valid && $stored) {
        SecurityLog::create([
            'user_id' => auth()->id(),
            'event' => 'session_fingerprint_mismatch',
            'description' => 'Session fingerprint mismatch during verification',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'severity' => 'warning',
        ]);
    }

    return response()->json(['valid' => $valid]);
})->middleware('auth');
