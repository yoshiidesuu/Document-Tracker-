@extends('layouts.system')

@section('title', \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker')) . ' - Permissions')

@section('page_title', 'Permission Manager')

@section('content')
<div class="w-full">
    @if (session('success'))
        <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 p-4">
            <div class="flex items-start space-x-3">
                <svg class="h-5 w-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
            <div class="flex items-start space-x-3">
                <svg class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <div class="text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Role</span>
                    <select id="role-select" class="px-2.5 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected(request('role', $roles->first()->id) == $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button" id="global-select-all" class="px-2.5 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100 transition-colors">Select All</button>
                    <button type="button" id="global-unselect-all" class="px-2.5 py-1.5 text-xs font-medium text-gray-500 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors">Unselect All</button>
                </div>
                <div class="relative">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" id="perm-search" placeholder="Search permissions..." title="Press / to search" autocomplete="off" class="w-60 pl-8 pr-8 py-1.5 text-xs border border-gray-300 rounded-md bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-colors">
                    <button type="button" id="perm-search-clear" class="absolute right-2 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-gray-400 hover:text-gray-600 transition-colors hidden">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="px-6 py-5 space-y-6" id="perm-sections">
            @php $selectedRole = $roles->firstWhere('id', request('role', $roles->first()->id)); @endphp

            @foreach ($sidebarItems as $sidebarKey => $sidebarItem)
                @php
                    $sidebarEnabled = $selectedRole && $selectedRole->hasPermission($sidebarKey);
                    $allFeatureKeys = array_keys($sidebarItem['features']);
                @endphp

                <div class="border rounded-lg overflow-hidden perm-section {{ $sidebarEnabled ? 'border-indigo-200' : 'border-gray-200' }}" data-sidebar="{{ $sidebarKey }}">
                    <label class="flex items-center px-5 py-3 cursor-pointer transition-colors section-header {{ $sidebarEnabled ? 'bg-indigo-50' : 'bg-gray-50 hover:bg-gray-100' }}">
                        <div class="flex items-center h-5">
                            <input type="checkbox" value="{{ $sidebarKey }}" {{ $sidebarEnabled ? 'checked' : '' }} class="sidebar-checkbox h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" data-sidebar="{{ $sidebarKey }}">
                        </div>
                        <span class="ml-3 text-sm font-semibold text-gray-900">{{ $sidebarItem['label'] }}</span>
                        <span class="ml-2 text-xs text-gray-400">{{ count($sidebarItem['features']) }} feature(s)</span>
                        <div class="ml-auto flex items-center gap-2">
                            <button type="button" class="section-select-all text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">Select All</button>
                            <span class="text-xs text-gray-300">|</span>
                            <button type="button" class="section-unselect-all text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Unselect All</button>
                        </div>
                    </label>

                    @if(count($sidebarItem['features']) > 0)
                        <div class="border-t {{ $sidebarEnabled ? 'border-indigo-100' : 'border-gray-100' }}">
                            <div class="px-5 py-3 space-y-1">
                                @foreach ($sidebarItem['features'] as $featureKey => $featureLabel)
                                    @php $featureEnabled = $selectedRole && $selectedRole->hasPermission($featureKey); @endphp
                                    <label class="feature-label flex items-center px-3 py-2 rounded-md cursor-pointer transition-colors {{ $featureEnabled ? 'bg-indigo-50/50' : 'hover:bg-gray-50' }}" data-search="{{ strtolower($featureLabel . ' ' . $featureKey) }}">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" value="{{ $featureKey }}" {{ $featureEnabled ? 'checked' : '' }} class="feature-checkbox h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" data-sidebar="{{ $sidebarKey }}">
                                        </div>
                                        <div class="ml-3 flex-1 min-w-0">
                                            <span class="text-sm text-gray-700">{{ $featureLabel }}</span>
                                            <code class="block text-xs text-gray-400 truncate">{{ $featureKey }}</code>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div id="no-results" class="hidden px-6 py-16 text-center">
            <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">No permissions match your search.</p>
            <p class="mt-1 text-xs text-gray-400">Try a different keyword or <button type="button" id="no-results-clear" class="text-indigo-600 hover:text-indigo-800 underline">clear search</button>.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function () {
    var roleId = document.getElementById('role-select').value;
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function togglePermission(checkbox) {
        var permission = checkbox.value;
        var enabled = checkbox.checked;
        var roleId = document.getElementById('role-select').value;

        fetch('{{ route('system.permissions.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                role_id: roleId,
                permission: permission,
                enabled: enabled,
            }),
        }).catch(function () {
            checkbox.checked = !enabled;
        });
    }

    function updateSectionStyle(section) {
        var sidebarCheckbox = section.querySelector('.sidebar-checkbox');
        var header = section.querySelector('.section-header');
        var borderTop = section.querySelector('.border-t');
        var enabled = sidebarCheckbox.checked;

        section.classList.remove('border-indigo-200', 'border-gray-200');
        section.classList.add(enabled ? 'border-indigo-200' : 'border-gray-200');

        header.classList.remove('bg-indigo-50', 'bg-gray-50', 'hover:bg-gray-100');
        header.classList.add(enabled ? 'bg-indigo-50' : 'bg-gray-50 hover:bg-gray-100');

        if (borderTop) {
            borderTop.classList.remove('border-indigo-100', 'border-gray-100');
            borderTop.classList.add(enabled ? 'border-indigo-100' : 'border-gray-100');
        }

        section.querySelectorAll('.feature-label').forEach(function (label) {
            var cb = label.querySelector('.feature-checkbox');
            if (cb && cb.checked) {
                label.classList.remove('bg-indigo-50/50', 'hover:bg-gray-50');
                label.classList.add('bg-indigo-50/50');
            } else {
                label.classList.remove('bg-indigo-50/50', 'hover:bg-gray-50');
                label.classList.add('hover:bg-gray-50');
            }
        });
    }

    function applySearch(query) {
        query = query.toLowerCase().trim();
        var hasVisible = false;

        document.querySelectorAll('.perm-section').forEach(function (section) {
            var anyVisible = false;
            var features = section.querySelectorAll('.feature-label');

            features.forEach(function (label) {
                var text = label.getAttribute('data-search') || '';
                var match = !query || text.indexOf(query) !== -1;
                label.style.display = match ? '' : 'none';
                if (match) anyVisible = true;
            });

            var featureContainer = section.querySelector('.border-t');
            if (featureContainer) {
                featureContainer.style.display = anyVisible || !query ? '' : 'none';
            }

            section.style.display = anyVisible || !query ? '' : 'none';
            if (anyVisible || !query) hasVisible = true;
        });

        document.getElementById('no-results').classList.toggle('hidden', hasVisible);
        document.getElementById('perm-sections').classList.toggle('hidden', !hasVisible);
    }

    document.querySelectorAll('.sidebar-checkbox').forEach(function (cb) {
        cb.addEventListener('change', function () {
            var section = this.closest('.perm-section');
            togglePermission(this);
            updateSectionStyle(section);
        });
    });

    document.querySelectorAll('.feature-checkbox').forEach(function (cb) {
        cb.addEventListener('change', function () {
            var label = this.closest('.feature-label');
            var section = this.closest('.perm-section');

            label.classList.remove('bg-indigo-50/50', 'hover:bg-gray-50');
            label.classList.add(this.checked ? 'bg-indigo-50/50' : 'hover:bg-gray-50');

            togglePermission(this);
        });
    });

    document.querySelectorAll('.section-select-all').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var section = this.closest('.perm-section');
            var checkboxes = section.querySelectorAll('.feature-checkbox, .sidebar-checkbox');
            checkboxes.forEach(function (cb) {
                if (!cb.checked) {
                    cb.checked = true;
                    cb.dispatchEvent(new Event('change'));
                }
            });
        });
    });

    document.querySelectorAll('.section-unselect-all').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var section = this.closest('.perm-section');
            var checkboxes = section.querySelectorAll('.feature-checkbox');
            checkboxes.forEach(function (cb) {
                if (cb.checked) {
                    cb.checked = false;
                    cb.dispatchEvent(new Event('change'));
                }
            });
        });
    });

    document.getElementById('global-select-all').addEventListener('click', function () {
        document.querySelectorAll('.sidebar-checkbox, .feature-checkbox').forEach(function (cb) {
            if (!cb.checked) {
                cb.checked = true;
                cb.dispatchEvent(new Event('change'));
            }
        });
    });

    document.getElementById('global-unselect-all').addEventListener('click', function () {
        document.querySelectorAll('.feature-checkbox').forEach(function (cb) {
            if (cb.checked) {
                cb.checked = false;
                cb.dispatchEvent(new Event('change'));
            }
        });
    });

    document.getElementById('role-select').addEventListener('change', function () {
        var url = new URL(window.location);
        url.searchParams.set('role', this.value);
        window.location.href = url.toString();
    });

    var searchInput = document.getElementById('perm-search');
    var searchClear = document.getElementById('perm-search-clear');
    var searchTimer;

    function updateClearButton() {
        if (searchInput.value.length > 0) {
            searchClear.classList.remove('hidden');
        } else {
            searchClear.classList.add('hidden');
        }
    }

    searchInput.addEventListener('input', function () {
        updateClearButton();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            applySearch(searchInput.value);
        }, 150);
    });

    function clearSearch() {
        searchInput.value = '';
        searchInput.focus();
        updateClearButton();
        applySearch('');
    }

    searchClear.addEventListener('click', clearSearch);

    var noResultsClear = document.getElementById('no-results-clear');
    if (noResultsClear) {
        noResultsClear.addEventListener('click', clearSearch);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === '/' && !e.ctrlKey && !e.metaKey && document.activeElement !== searchInput) {
            e.preventDefault();
            searchInput.focus();
        }
    });
});
</script>
@endpush
