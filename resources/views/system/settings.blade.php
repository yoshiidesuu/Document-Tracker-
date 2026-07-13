@extends('layouts.system')

@section('title', config('app.name', 'Document Tracker') . ' - System Settings')

@section('page_title', 'System Settings')

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

    <form method="POST" action="{{ route('system.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">General Settings</h2>
                <p class="text-sm text-gray-500 mt-1">System branding and appearance.</p>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Logo</label>
                        <div class="space-y-3">
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 flex items-center justify-center min-h-[180px] bg-gray-50/50" id="logoPreviewContainer">
                                @if ($settings['site_logo'])
                                    <img src="{{ route('file.logo') }}" alt="Logo" class="max-h-[150px] max-w-full object-contain" id="logoPreviewImg"  style="-webkit-user-drag: none; user-select: none;">
                                @else
                                    <div class="text-center" id="logoPreviewPlaceholder">
                                        <svg class="h-12 w-12 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.41a2.25 2.25 0 013.182 0l2.909 2.91m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                        <p class="text-sm text-gray-400">No logo uploaded</p>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center space-x-3">
                                <input type="file" name="site_logo" id="site_logo" accept="image/png,image/svg+xml,image/jpeg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors">
                            </div>
                            <p class="text-xs text-gray-400">PNG, SVG, or JPG. Max 15MB.</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Favicon</label>
                        <div class="space-y-3">
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 flex items-center justify-center min-h-[180px] bg-gray-50/50" id="faviconPreviewContainer">
                                @if ($settings['site_favicon'])
                                    <img src="{{ route('favicon') }}" alt="Favicon" class="max-h-[150px] max-w-full object-contain" id="faviconPreviewImg"  style="-webkit-user-drag: none; user-select: none;">
                                @else
                                    <div class="text-center" id="faviconPreviewPlaceholder">
                                        <svg class="h-12 w-12 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.41a2.25 2.25 0 013.182 0l2.909 2.91m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                        <p class="text-sm text-gray-400">No favicon uploaded</p>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center space-x-3">
                                <input type="file" name="site_favicon" id="site_favicon" accept="image/png,image/svg+xml,image/jpeg,image/x-icon" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors">
                            </div>
                            <p class="text-xs text-gray-400">PNG, SVG, JPG, or ICO. Max 15MB.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="site_long_name" class="block text-sm font-medium text-gray-700 mb-1">System Name</label>
                        <input type="text" id="site_long_name" name="site_long_name" value="{{ old('site_long_name', $settings['site_long_name']) }}" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                    </div>
                    <div>
                        <label for="site_short_name" class="block text-sm font-medium text-gray-700 mb-1">System Short Name</label>
                        <input type="text" id="site_short_name" name="site_short_name" value="{{ old('site_short_name', $settings['site_short_name']) }}" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                    </div>
                </div>

                <div>
                    <label for="site_description" class="block text-sm font-medium text-gray-700 mb-1">System Description</label>
                    <textarea id="site_description" name="site_description" rows="2" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">{{ old('site_description', $settings['site_description']) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" id="color_primary" name="color_primary" value="{{ old('color_primary', $settings['color_primary']) }}" class="h-10 w-10 p-0.5 border border-gray-300 rounded cursor-pointer">
                            <input type="text" value="{{ old('color_primary', $settings['color_primary']) }}" class="block w-28 px-3 py-2 border border-gray-300 rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Color</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" id="color_secondary" name="color_secondary" value="{{ old('color_secondary', $settings['color_secondary']) }}" class="h-10 w-10 p-0.5 border border-gray-300 rounded cursor-pointer">
                            <input type="text" value="{{ old('color_secondary', $settings['color_secondary']) }}" class="block w-28 px-3 py-2 border border-gray-300 rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Document Settings</h2>
                <p class="text-sm text-gray-500 mt-1">Header configuration for generated documents.</p>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Document Right Logo</label>
                        <div class="space-y-3">
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 flex items-center justify-center min-h-[180px] bg-gray-50/50" id="docLogoRightPreviewContainer">
                                @if ($settings['document_right_logo'])
                                    <img src="{{ route('file.document-logo-right') }}" alt="Right Logo" class="max-h-[150px] max-w-full object-contain" id="docLogoRightPreviewImg"  style="-webkit-user-drag: none; user-select: none;">
                                @else
                                    <div class="text-center" id="docLogoRightPreviewPlaceholder">
                                        <svg class="h-12 w-12 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.41a2.25 2.25 0 013.182 0l2.909 2.91m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                        <p class="text-sm text-gray-400">No logo uploaded</p>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center space-x-3">
                                <input type="file" name="document_right_logo" id="document_right_logo" accept="image/png,image/svg+xml,image/jpeg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors">
                            </div>
                            <p class="text-xs text-gray-400">PNG, SVG, or JPG. Max 15MB.</p>
                        </div>
                    </div>
                    <div>
                        <label for="document_header_title" class="block text-sm font-medium text-gray-700 mb-1">Document Header Title</label>
                        <input type="text" id="document_header_title" name="document_header_title" value="{{ old('document_header_title', $settings['document_header_title']) }}" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                        <p class="text-xs text-gray-400 mt-1">Appears centered in the document header.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Contact Information</h2>
                <p class="text-sm text-gray-500 mt-1">Email addresses, contact numbers, and office addresses.</p>
            </div>
            <div class="px-6 py-5 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Addresses</label>
                    <div id="emailsContainer" class="space-y-2">
                        @if (count($settings['emails']))
                            @foreach ($settings['emails'] as $email)
                                <div class="flex items-center space-x-2">
                                    <input type="email" name="emails[]" value="{{ $email }}" placeholder="email@example.com" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                                    <button type="button" class="remove-row p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-center space-x-2">
                                <input type="email" name="emails[]" placeholder="email@example.com" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                                <button type="button" class="remove-row p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" id="addEmail" class="mt-2 text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors">+ Add another email</button>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Numbers</label>
                    <div id="contactsContainer" class="space-y-2">
                        @if (count($settings['contacts']))
                            @foreach ($settings['contacts'] as $contact)
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="contacts[]" value="{{ $contact }}" placeholder="+63 912 345 6789" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                                    <button type="button" class="remove-row p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-center space-x-2">
                                <input type="text" name="contacts[]" placeholder="+63 912 345 6789" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                                <button type="button" class="remove-row p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" id="addContact" class="mt-2 text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors">+ Add another contact number</button>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Addresses</label>
                    <div id="addressesContainer" class="space-y-2">
                        @if (count($settings['addresses']))
                            @foreach ($settings['addresses'] as $address)
                                <div class="flex items-start space-x-2">
                                    <textarea name="addresses[]" rows="2" placeholder="Building, Street, City, Province" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">{{ $address }}</textarea>
                                    <button type="button" class="remove-row p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors mt-1">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-start space-x-2">
                                <textarea name="addresses[]" rows="2" placeholder="Building, Street, City, Province" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"></textarea>
                                <button type="button" class="remove-row p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors mt-1">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" id="addAddress" class="mt-2 text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors">+ Add another address</button>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('system.dashboard') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function () {
    function addRow(containerId, inputType, placeholder) {
        var container = document.getElementById(containerId);
        var row = document.createElement('div');
        row.className = containerId === 'addressesContainer' ? 'flex items-start space-x-2' : 'flex items-center space-x-2';
        var input;
        if (inputType === 'textarea') {
            input = document.createElement('textarea');
            input.rows = 2;
        } else {
            input = document.createElement('input');
            input.type = inputType;
        }
        input.name = containerId === 'emailsContainer' ? 'emails[]' : containerId === 'contactsContainer' ? 'contacts[]' : 'addresses[]';
        input.placeholder = placeholder;
        input.className = containerId === 'addressesContainer'
            ? 'block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm'
            : 'block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm';
        row.appendChild(input);

        var removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-row p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors' + (inputType === 'textarea' ? ' mt-1' : '');
        removeBtn.innerHTML = '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>';
        removeBtn.addEventListener('click', function () { row.remove(); });
        row.appendChild(removeBtn);
        container.appendChild(row);
    }

    document.getElementById('addEmail').addEventListener('click', function () {
        addRow('emailsContainer', 'email', 'email@example.com');
    });
    document.getElementById('addContact').addEventListener('click', function () {
        addRow('contactsContainer', 'text', '+63 912 345 6789');
    });
    document.getElementById('addAddress').addEventListener('click', function () {
        addRow('addressesContainer', 'textarea', 'Building, Street, City, Province');
    });

    document.querySelectorAll('#emailsContainer, #contactsContainer, #addressesContainer').forEach(function (container) {
        container.addEventListener('click', function (e) {
            if (e.target.closest('.remove-row')) {
                var row = e.target.closest('.remove-row').parentNode;
                if (container.children.length > 1) row.remove();
            }
        });
    });

    function handleFilePreview(inputId, containerId, imgId, placeholderId) {
        var input = document.getElementById(inputId);
        if (!input) return;
        input.addEventListener('change', function (e) {
            var file = e.target.files[0];
            var container = document.getElementById(containerId);
            if (!file) {
                window.location.reload();
                return;
            }
            var reader = new FileReader();
            reader.onload = function (ev) {
                var placeholder = document.getElementById(placeholderId);
                if (placeholder) placeholder.remove();
                var existingImg = document.getElementById(imgId);
                if (existingImg) {
                    existingImg.src = ev.target.result;
                } else {
                    var img = document.createElement('img');
                    img.id = imgId;
                    img.src = ev.target.result;
                    img.alt = 'New Preview';
                    img.className = 'max-h-[150px] max-w-full object-contain';
                    img.setAttribute('ondragstart', 'return false');
                    img.setAttribute('oncontextmenu', 'return false');
                    img.style.cssText = '-webkit-user-drag: none; user-select: none;';
                    container.innerHTML = '';
                    container.appendChild(img);
                }
            };
            reader.readAsDataURL(file);
        });
    }

    handleFilePreview('site_logo', 'logoPreviewContainer', 'logoPreviewImg', 'logoPreviewPlaceholder');
    handleFilePreview('site_favicon', 'faviconPreviewContainer', 'faviconPreviewImg', 'faviconPreviewPlaceholder');
    handleFilePreview('document_right_logo', 'docLogoRightPreviewContainer', 'docLogoRightPreviewImg', 'docLogoRightPreviewPlaceholder');

    var colorPrimary = document.getElementById('color_primary');
    var colorSecondary = document.getElementById('color_secondary');
    if (colorPrimary) {
        colorPrimary.addEventListener('input', function () {
            this.nextElementSibling.value = this.value;
        });
    }
    if (colorSecondary) {
        colorSecondary.addEventListener('input', function () {
            this.nextElementSibling.value = this.value;
        });
    }
});
</script>
@endpush
