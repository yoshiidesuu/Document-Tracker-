class Messages {
    constructor() {
        this.activeUserId = null;
        this.lastMessageId = 0;
        this.pollInterval = null;
        this.conversations = [];
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        this.currentUserId = document.querySelector('meta[name="chat-user-id"]')?.content || null;

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadConversations();
        this.startPolling();
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            const convItem = e.target.closest('[data-conversation-id]');
            if (convItem) { this.openConversation(convItem.dataset.conversationId); return; }

            const searchUser = e.target.closest('[data-search-user-id]');
            if (searchUser) { this.startChatWith(searchUser.dataset); return; }

            const back = e.target.closest('#backToConv');
            if (back) { this.showConversations(); return; }
        });

        const searchInput = document.getElementById('userSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const q = e.target.value.trim();
                if (q.length < 1) { this.hideSearch(); return; }
                clearTimeout(this._searchDebounce);
                this._searchDebounce = setTimeout(() => this.searchUsers(q), 250);
            });
            searchInput.addEventListener('focus', () => {
                const q = searchInput.value.trim();
                if (q.length >= 1) document.getElementById('searchResults')?.classList.remove('hidden');
            });
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#userSearch') && !e.target.closest('#searchResults')) {
                    this.hideSearch();
                }
            });
        }

        const form = document.getElementById('messageForm');
        const input = document.getElementById('messageInput');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const msg = input?.value.trim();
                if (!msg || !this.activeUserId) return;
                this.sendMessage(msg);
            });
        }
        if (input) {
            input.addEventListener('input', () => {
                const btn = document.getElementById('sendBtn');
                if (btn) btn.disabled = !input.value.trim();
            });
        }
    }

    startPolling() {
        this.stopPolling();
        this.pollInterval = setInterval(() => {
            if (this.activeUserId) this.pollMessages();
            this.loadConversations(true);
        }, 3000);
    }

    stopPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
    }

    async api(path, opts = {}) {
        try {
            const res = await fetch(path, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.csrfToken,
                    ...opts.headers,
                },
                ...opts,
            });
            if (!res.ok) return null;
            return await res.json();
        } catch { return null; }
    }

    async loadConversations(silent = false) {
        const data = await this.api('/api/chat/conversations');
        if (!data) return;

        this.conversations = data.conversations || [];

        const loading = document.getElementById('convLoading');
        const empty = document.getElementById('convEmpty');
        const list = document.getElementById('convList');

        if (loading) loading.classList.add('hidden');
        if (empty) empty.classList.add('hidden');

        if (this.conversations.length === 0) {
            if (empty) empty.classList.remove('hidden');
            list?.querySelectorAll('.conv-item').forEach(el => el.remove());
        } else {
            list?.querySelectorAll('.conv-item').forEach(el => el.remove());
            this.conversations.forEach(c => this.renderConv(c));
        }

        if (this.activeUserId && !silent) {
            const c = this.conversations.find(x => x.id == this.activeUserId);
            if (c) this.updateConvItem(c);
        }
    }

    renderConv(conv) {
        const list = document.getElementById('convList');
        if (!list || document.querySelector(`[data-conversation-id="${conv.id}"]`)) return;

        const div = document.createElement('div');
        div.className = `conv-item flex items-center px-4 py-3 cursor-pointer hover:bg-gray-100 transition-colors border-b border-gray-100 ${conv.unread_count > 0 ? 'bg-indigo-50/60' : ''}`;
        div.dataset.conversationId = conv.id;
        div.id = `conv-${conv.id}`;

        const avatar = conv.profile_picture
            ? `<img src="${conv.profile_picture}" alt="" class="h-10 w-10 rounded-full object-cover flex-shrink-0">`
            : `<div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-semibold text-indigo-600 flex-shrink-0">${this.esc(conv.initials || '?')}</div>`;

        const badge = conv.unread_count > 0
            ? `<span class="ml-auto min-w-[20px] h-5 flex items-center justify-center px-1.5 text-[10px] font-bold text-white bg-indigo-600 rounded-full">${conv.unread_count > 99 ? '99+' : conv.unread_count}</span>`
            : '';

        div.innerHTML = `
            <div class="flex items-center space-x-3 flex-1 min-w-0">
                ${avatar}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900 truncate">${this.esc(conv.name)}</p>
                        ${conv.last_message ? `<span class="text-[10px] text-gray-400 ml-2 flex-shrink-0">${this.esc(conv.last_message.created_at)}</span>` : ''}
                    </div>
                    <p class="text-xs text-gray-500 truncate mt-0.5">${conv.last_message ? (conv.last_message.is_mine ? 'You: ' : '') + this.esc(conv.last_message.message) : 'No messages yet'}</p>
                </div>
                ${badge}
            </div>
        `;

        list.appendChild(div);
    }

    updateConvItem(conv) {
        const el = document.getElementById(`conv-${conv.id}`);
        if (!el) return;

        const avatar = conv.profile_picture
            ? `<img src="${conv.profile_picture}" alt="" class="h-10 w-10 rounded-full object-cover flex-shrink-0">`
            : `<div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-semibold text-indigo-600 flex-shrink-0">${this.esc(conv.initials || '?')}</div>`;

        const badge = conv.unread_count > 0
            ? `<span class="ml-auto min-w-[20px] h-5 flex items-center justify-center px-1.5 text-[10px] font-bold text-white bg-indigo-600 rounded-full">${conv.unread_count > 99 ? '99+' : conv.unread_count}</span>`
            : '';

        el.className = `conv-item flex items-center px-4 py-3 cursor-pointer hover:bg-gray-100 transition-colors border-b border-gray-100 ${conv.unread_count > 0 ? 'bg-indigo-50/60' : ''}`;

        const inner = el.querySelector('.flex.items-center.space-x-3');
        if (inner) {
            inner.innerHTML = `
                ${avatar}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900 truncate">${this.esc(conv.name)}</p>
                        ${conv.last_message ? `<span class="text-[10px] text-gray-400 ml-2 flex-shrink-0">${this.esc(conv.last_message.created_at)}</span>` : ''}
                    </div>
                    <p class="text-xs text-gray-500 truncate mt-0.5">${conv.last_message ? (conv.last_message.is_mine ? 'You: ' : '') + this.esc(conv.last_message.message) : 'No messages yet'}</p>
                </div>
                ${badge}
            `;
        }
    }

    async openConversation(userId) {
        this.activeUserId = userId;
        this.lastMessageId = 0;

        document.getElementById('convPanel')?.classList.add('hidden');
        document.getElementById('convPanel')?.classList.remove('lg:flex');
        document.getElementById('chatPanel')?.classList.add('hidden');
        const chat = document.getElementById('activeChat');
        if (chat) {
            chat.classList.remove('hidden');
            chat.classList.add('flex');
        }

        const conv = this.conversations.find(c => c.id == userId);
        const avatar = document.getElementById('activeChatAvatar');
        const name = document.getElementById('activeChatName');
        if (avatar) {
            avatar.innerHTML = conv?.profile_picture
                ? `<img src="${conv.profile_picture}" alt="" class="h-8 w-8 rounded-full object-cover">`
                : this.esc(conv?.initials || '?');
        }
        if (name) name.textContent = conv?.name || 'Unknown User';

        const list = document.getElementById('messageList');
        if (list) list.innerHTML = '<div class="flex items-center justify-center py-8"><svg class="animate-spin h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';

        const data = await this.api(`/api/chat/${userId}`);
        if (!data) return;

        if (list) list.innerHTML = '';
        (data.data || []).forEach(msg => this.renderMsg(msg));
        this.scrollBottom();

        if (data.data?.length) this.lastMessageId = data.data[data.data.length - 1].id;

        document.getElementById('messageInput')?.focus();

        if (conv) {
            conv.unread_count = 0;
            this.updateConvItem(conv);
        }
    }

    async pollMessages() {
        if (!this.activeUserId) return;
        const data = await this.api(`/api/chat/${this.activeUserId}/poll?after=${this.lastMessageId}`);
        if (!data?.length) return;

        data.forEach(msg => {
            this.renderMsg(msg);
            if (msg.id > this.lastMessageId) this.lastMessageId = msg.id;
        });
        this.scrollBottom();
        this.loadConversations(true);
    }

    renderMsg(msg) {
        const list = document.getElementById('messageList');
        if (!list || document.getElementById(`msg-${msg.id}`)) return;

        const isMine = msg.sender_id == this.currentUserId;
        const div = document.createElement('div');
        div.id = `msg-${msg.id}`;
        div.className = `flex ${isMine ? 'justify-end' : 'justify-start'}`;

        const time = msg.created_at
            ? new Date(msg.created_at + ' UTC').toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            : '';

        div.innerHTML = `
            <div class="max-w-[75%] ${isMine ? 'bg-indigo-600 text-white rounded-2xl rounded-br-sm' : 'bg-white text-gray-900 rounded-2xl rounded-bl-sm shadow-sm'} px-3.5 py-2.5">
                <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">${this.esc(msg.message)}</p>
                <p class="text-[10px] mt-1 ${isMine ? 'text-indigo-200' : 'text-gray-400'} text-right">${time}</p>
            </div>
        `;

        list.appendChild(div);
    }

    async sendMessage(text) {
        const input = document.getElementById('messageInput');
        const btn = document.getElementById('sendBtn');
        if (!text || !this.activeUserId) return;
        if (input) input.value = '';
        if (btn) btn.disabled = true;

        const data = await this.api(`/api/chat/${this.activeUserId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: text }),
        });

        if (data) {
            this.renderMsg(data);
            this.scrollBottom();
            this.lastMessageId = data.id;
            this.loadConversations(true);
        }
    }

    showConversations() {
        this.activeUserId = null;
        this.lastMessageId = 0;
        document.getElementById('activeChat')?.classList.add('hidden');
        document.getElementById('activeChat')?.classList.remove('flex');
        document.getElementById('chatPanel')?.classList.remove('hidden');
        document.getElementById('convPanel')?.classList.remove('hidden');
        document.getElementById('convPanel')?.classList.add('lg:flex');
    }

    async searchUsers(q) {
        const data = await this.api(`/api/chat/users/search?q=${encodeURIComponent(q)}`);
        const container = document.getElementById('searchResults');
        if (!container) return;

        if (!data?.length) {
            container.innerHTML = '<div class="p-4 text-sm text-gray-500 text-center">No users found</div>';
            container.classList.remove('hidden');
            return;
        }

        container.innerHTML = data.map(u => `
            <div data-search-user-id="${u.id}"
                 data-name="${this.escAttr(u.name)}"
                 data-firstname="${this.escAttr(u.firstname)}"
                 data-lastname="${this.escAttr(u.lastname)}"
                 data-profile-picture="${this.escAttr(u.profile_picture || '')}"
                 data-initials="${this.escAttr(u.initials || '')}"
                 class="flex items-center space-x-3 px-4 py-2.5 cursor-pointer hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0">
                ${u.profile_picture
                    ? `<img src="${u.profile_picture}" alt="" class="h-9 w-9 rounded-full object-cover flex-shrink-0">`
                    : `<div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-semibold text-indigo-600 flex-shrink-0">${this.esc(u.initials || '?')}</div>`}
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">${this.esc(u.name)}</p>
                </div>
                <span class="text-xs text-indigo-600 font-medium">Chat</span>
            </div>
        `).join('');
        container.classList.remove('hidden');
    }

    startChatWith(data) {
        this.hideSearch();
        document.getElementById('userSearch').value = '';

        const userId = parseInt(data.searchUserId);
        const existing = this.conversations.find(c => c.id == userId);
        if (existing) {
            this.openConversation(userId);
            return;
        }

        const conv = {
            id: userId,
            name: data.name || 'User',
            firstname: data.firstname || '',
            lastname: data.lastname || '',
            profile_picture: data.profilePicture || null,
            initials: data.initials || '?',
            last_message: null,
            unread_count: 0,
        };
        this.conversations.unshift(conv);
        this.renderConv(conv);
        this.openConversation(userId);
    }

    hideSearch() {
        document.getElementById('searchResults')?.classList.add('hidden');
    }

    scrollBottom() {
        const list = document.getElementById('messageList');
        if (list) list.scrollTop = list.scrollHeight;
    }

    esc(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    escAttr(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('messagesApp')) {
        new Messages();
    }
});
