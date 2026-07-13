document.addEventListener('DOMContentLoaded', function () {
    var loginForm = document.getElementById('loginForm');
    if (!loginForm) return;

    var credentialInput = document.getElementById('credential');
    var credentialStatus = document.getElementById('credentialStatus');
    var credentialHelp = document.getElementById('credentialHelp');
    var loginBtn = document.getElementById('loginBtn');
    var toggleBtn = document.getElementById('togglePassword');
    var passwordInput = document.getElementById('password');
    var eyeIcon = document.getElementById('eyeIcon');
    var rememberCheck = document.querySelector('input[name="remember"]');
    var checkUrl = loginForm.dataset.checkUrl;
    var debounceTimer = null;
    var currentCheckedUser = null;

    var SAVED_ACCOUNTS_KEY = 'dtr_saved_accounts';

    function getSavedAccounts() {
        try { return JSON.parse(localStorage.getItem(SAVED_ACCOUNTS_KEY)) || []; } catch (e) { return []; }
    }

    function saveAccount(account) {
        var accounts = getSavedAccounts();
        var idx = accounts.findIndex(function (a) { return a.credential === account.credential; });
        if (idx !== -1) { accounts[idx] = account; } else { accounts.push(account); }
        localStorage.setItem(SAVED_ACCOUNTS_KEY, JSON.stringify(accounts));
        renderSavedAccounts();
    }

    function removeAccount(credential) {
        var accounts = getSavedAccounts().filter(function (a) { return a.credential !== credential; });
        localStorage.setItem(SAVED_ACCOUNTS_KEY, JSON.stringify(accounts));
        renderSavedAccounts();
    }

    function escapeHtml(str) {
        if (!str) return '';
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    function getInitials(name) {
        if (!name) return '?';
        var parts = name.trim().split(/\s+/);
        if (parts.length >= 2) return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        return name.substring(0, 2).toUpperCase();
    }

    function renderSavedAccounts() {
        var container = document.getElementById('savedAccountsContainer');
        if (!container) return;
        var accounts = getSavedAccounts();
        if (accounts.length === 0) { container.classList.add('hidden'); return; }
        container.classList.remove('hidden');

        var html = '<div class="mb-2"><p class="text-sm font-medium text-gray-700 mb-3">Saved Accounts</p><div class="space-y-2">';
        for (var i = 0; i < accounts.length; i++) {
            var a = accounts[i];
            var pic = a.profile_picture
                ? '<img src="' + escapeHtml(a.profile_picture) + '" alt="" class="h-10 w-10 rounded-full object-cover flex-shrink-0">'
                : '<div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-semibold text-indigo-600 flex-shrink-0">' + getInitials(a.name) + '</div>';
            html += '<div class="saved-account-card flex items-center space-x-3 p-3 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 cursor-pointer transition-all" data-credential="' + escapeHtml(a.credential) + '" data-password="' + escapeHtml(a.password || '') + '">';
            html += pic;
            html += '<div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-900 truncate">' + escapeHtml(a.name) + '</p><p class="text-xs text-gray-500 truncate">' + escapeHtml(a.credential) + '</p></div>';
            html += '<button class="remove-account p-1 rounded-full text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors flex-shrink-0" data-credential="' + escapeHtml(a.credential) + '" title="Remove saved account">';
            html += '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>';
            html += '</button></div>';
        }
        html += '</div></div>';
        container.innerHTML = html;

        container.querySelectorAll('.saved-account-card').forEach(function (card) {
            card.addEventListener('click', function (e) {
                if (e.target.closest('.remove-account')) return;
                credentialInput.value = this.dataset.credential;
                var pw = this.dataset.password;
                if (pw) passwordInput.value = pw;
                loginBtn.click();
            });
        });

        container.querySelectorAll('.remove-account').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                removeAccount(this.dataset.credential);
            });
        });
    }

    renderSavedAccounts();

    function updateCredentialUI(user) {
        currentCheckedUser = user;
        credentialStatus.innerHTML = '';
        credentialHelp.classList.add('hidden');
        credentialHelp.classList.remove('text-gray-500', 'text-emerald-600', 'text-red-500');
        credentialInput.classList.remove('border-emerald-300', 'focus:ring-emerald-500', 'focus:border-emerald-500');
        credentialInput.classList.remove('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');

        if (user) {
            credentialStatus.innerHTML =
                '<svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">' +
                '  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />' +
                '</svg>';
            credentialHelp.classList.remove('hidden');
            credentialHelp.classList.add('text-emerald-600');
            credentialHelp.innerHTML = 'User found: <strong>' + user.firstname + ' ' + user.lastname + '</strong>';
            credentialInput.classList.add('border-emerald-300', 'focus:ring-emerald-500', 'focus:border-emerald-500');
            loginBtn.disabled = false;
        } else {
            credentialStatus.innerHTML =
                '<svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">' +
                '  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />' +
                '</svg>';
            credentialHelp.classList.remove('hidden');
            credentialHelp.classList.add('text-red-500');
            credentialHelp.textContent = 'No account found with that email or ID number.';
            credentialInput.classList.add('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
            loginBtn.disabled = false;
        }
    }

    function clearValidationUI() {
        currentCheckedUser = null;
        credentialStatus.innerHTML = '';
        credentialHelp.classList.add('hidden');
        credentialHelp.classList.remove('text-emerald-600', 'text-red-500');
        credentialHelp.classList.add('text-gray-500');
        credentialInput.classList.remove('border-emerald-300', 'focus:ring-emerald-500', 'focus:border-emerald-500');
        credentialInput.classList.remove('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
        credentialInput.classList.add('border-gray-300');
        loginBtn.disabled = false;
    }

    function checkCredential(value) {
        if (value.length < 2) {
            clearValidationUI();
            return;
        }
        var xhr = new XMLHttpRequest();
        xhr.open('GET', checkUrl + '?q=' + encodeURIComponent(value), true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    updateCredentialUI(resp.exists ? resp.user : null);
                } catch (e) {
                    clearValidationUI();
                }
            } else {
                clearValidationUI();
            }
        };
        xhr.onerror = function () { clearValidationUI(); };
        xhr.send();
    }

    credentialInput.addEventListener('input', function () {
        var value = this.value.trim();
        if (debounceTimer) clearTimeout(debounceTimer);
        if (value.length < 2) { clearValidationUI(); return; }
        debounceTimer = setTimeout(function () { checkCredential(value); }, 400);
    });

    loginForm.addEventListener('submit', function () {
        if (rememberCheck && rememberCheck.checked && currentCheckedUser) {
            saveAccount({
                credential: credentialInput.value.trim(),
                password: passwordInput.value,
                name: currentCheckedUser.name,
                firstname: currentCheckedUser.firstname,
                lastname: currentCheckedUser.lastname,
                profile_picture: currentCheckedUser.profile_picture,
            });
        }
    });

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            var type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            eyeIcon.innerHTML = type === 'password'
                ? '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />'
                : '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />';
        });
    }
});

/* Sidebar toggle for system layout */
(function() {
    var sidebar = document.getElementById('sidebar');
    if (!sidebar) return;
    var overlay = document.getElementById('sidebarOverlay');
    var openBtn = document.getElementById('openSidebar');
    var closeBtn = document.getElementById('closeSidebar');

    function open() { sidebar.classList.remove('-translate-x-full'); if (overlay) overlay.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
    function close() { sidebar.classList.add('-translate-x-full'); if (overlay) overlay.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }

    if (openBtn) openBtn.addEventListener('click', open);
    if (closeBtn) closeBtn.addEventListener('click', close);
    if (overlay) overlay.addEventListener('click', close);
})();
