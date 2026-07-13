@extends('layouts.system')

@section('title', config('app.name', 'Document Tracker') . ' - Messages')

@section('page_title', 'Messages')

@section('content')
<div class="h-[calc(100vh-8rem)] -m-4 lg:-m-6 flex" id="messagesApp">
    <div id="convPanel" class="w-full lg:w-80 xl:w-96 border-r border-gray-200 bg-white flex flex-col flex-shrink-0">
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input
                    id="userSearch"
                    type="text"
                    placeholder="Search users..."
                    autocomplete="off"
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all"
                >
            </div>
            <div id="searchResults" class="hidden mt-1 bg-white border border-gray-200 rounded-xl shadow-xl max-h-64 overflow-y-auto absolute left-4 right-4 z-10"></div>
        </div>

        <div id="convList" class="flex-1 overflow-y-auto">
            <div id="convLoading" class="flex items-center justify-center py-12">
                <svg class="animate-spin h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div id="convEmpty" class="hidden flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="p-3 bg-gray-100 rounded-full mb-3">
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-600">No conversations yet</p>
                <p class="text-xs text-gray-400 mt-1">Search for a user above to start chatting</p>
            </div>
        </div>
    </div>

    <div id="chatPanel" class="hidden lg:flex flex-1 flex-col bg-gray-50 items-center justify-center">
        <div class="text-center">
            <div class="p-4 bg-white rounded-full shadow-sm inline-flex mb-4">
                <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-600">Select a conversation</h3>
            <p class="text-sm text-gray-400 mt-1">Choose a user from the left to start messaging</p>
        </div>
    </div>

    <div id="activeChat" class="hidden flex-1 flex-col bg-gray-50">
        <div class="flex items-center px-4 h-14 border-b border-gray-200 bg-white flex-shrink-0">
            <button id="backToConv" class="lg:hidden p-1 mr-3 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </button>
            <div id="activeChatUser" class="flex items-center space-x-3 flex-1 min-w-0">
                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-semibold text-indigo-600 flex-shrink-0" id="activeChatAvatar"></div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate" id="activeChatName"></p>
                </div>
            </div>
        </div>
        <div id="messageList" class="flex-1 overflow-y-auto px-4 py-4 space-y-3"></div>
        <div class="px-4 py-3 border-t border-gray-200 bg-white flex-shrink-0">
            <form id="messageForm" class="flex items-center space-x-3">
                <input
                    id="messageInput"
                    type="text"
                    placeholder="Type a message..."
                    autocomplete="off"
                    class="flex-1 px-4 py-2.5 text-sm border border-gray-300 rounded-xl bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all"
                >
                <button
                    id="sendBtn"
                    type="submit"
                    disabled
                    class="flex-shrink-0 p-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/messages.js'])
@endpush
