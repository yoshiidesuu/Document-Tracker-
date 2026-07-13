<footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 sm:gap-4">
            <div class="flex items-center space-x-1.5 sm:space-x-2 text-xs sm:text-sm text-gray-500">
                <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                <span>&copy; {{ date('Y') }} {{ \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker')) }}. All rights reserved.</span>
            </div>

            <div class="flex items-center space-x-4 sm:space-x-6 text-xs sm:text-sm">
                <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors">Privacy</a>
                <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors">Terms</a>
                <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors">Contact</a>
                @if (config('security.dict.data_privacy_officer_email'))
                    <a href="mailto:{{ config('security.dict.data_privacy_officer_email') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        DPO
                    </a>
                @endif
            </div>
        </div>
    </div>
</footer>
