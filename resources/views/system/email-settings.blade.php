@extends('layouts.system')

@section('title', config('app.name', 'Document Tracker') . ' - Email Settings')

@section('page_title', 'Email Settings')

@section('content')
<div>
    @if (session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-4">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="ml-3 text-sm text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">SMTP Configuration</h2>
            <p class="text-sm text-gray-500 mt-0.5">Configure your outgoing mail server settings.</p>
        </div>

        <form method="POST" action="{{ route('system.email-settings.update') }}" class="px-6 py-5 space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="smtp_host" class="block text-sm font-medium text-gray-700 mb-1.5">SMTP Server Address</label>
                    <input type="text" id="smtp_host" name="smtp_host" value="{{ old('smtp_host', $smtp_host) }}" required
                           class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm"
                           placeholder="smtp.gmail.com">
                    @error('smtp_host') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="smtp_port" class="block text-sm font-medium text-gray-700 mb-1.5">SMTP Port</label>
                    <input type="number" id="smtp_port" name="smtp_port" value="{{ old('smtp_port', $smtp_port) }}" required
                           class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm"
                           placeholder="587">
                    @error('smtp_port') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="smtp_encryption" class="block text-sm font-medium text-gray-700 mb-1.5">Secure Connection</label>
                    <select id="smtp_encryption" name="smtp_encryption" required
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm">
                        <option value="tls" {{ old('smtp_encryption', $smtp_encryption) === 'tls' ? 'selected' : '' }}>TLS (Port 587)</option>
                        <option value="ssl" {{ old('smtp_encryption', $smtp_encryption) === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                        <option value="none" {{ old('smtp_encryption', $smtp_encryption) === 'none' ? 'selected' : '' }}>None</option>
                    </select>
                    @error('smtp_encryption') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="smtp_username" class="block text-sm font-medium text-gray-700 mb-1.5">SMTP Username</label>
                    <input type="text" id="smtp_username" name="smtp_username" value="{{ old('smtp_username', $smtp_username) }}" required
                           class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm"
                           placeholder="user@gmail.com">
                    @error('smtp_username') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="smtp_password" class="block text-sm font-medium text-gray-700 mb-1.5">SMTP Password</label>
                    <div class="relative">
                        <input type="password" id="smtp_password" name="smtp_password" value="{{ old('smtp_password', $smtp_password) }}" required
                               class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm pr-10"
                               placeholder="Enter password">
                        <button type="button" id="togglePasswordBtn" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <svg id="pwEye" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('smtp_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="mail_from_address" class="block text-sm font-medium text-gray-700 mb-1.5">Sender Email</label>
                    <input type="email" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $mail_from_address) }}" required
                           class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm"
                           placeholder="noreply@example.com">
                    @error('mail_from_address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="mail_from_name" class="block text-sm font-medium text-gray-700 mb-1.5">Sender Name</label>
                    <input type="text" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $mail_from_name) }}" required
                           class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm"
                           placeholder="{{ config('app.name') }}">
                    @error('mail_from_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end pt-2 border-t border-gray-100">
                <button type="submit" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Send Test Email</h2>
            <p class="text-sm text-gray-500 mt-0.5">Send a test email to verify your SMTP configuration.</p>
        </div>

        <form method="POST" action="{{ route('system.email-settings.test') }}" class="px-6 py-5">
            @csrf
            <div class="flex items-end space-x-3">
                <div class="flex-1">
                    <label for="test_email" class="block text-sm font-medium text-gray-700 mb-1.5">Recipient Email</label>
                    <input type="email" id="test_email" name="test_email" value="{{ old('test_email') }}" required
                           class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm"
                           placeholder="you@example.com">
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors shadow-sm">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    Send Test
                </button>
            </div>
            @error('test_email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </form>
    </div>
</div>
@endsection


