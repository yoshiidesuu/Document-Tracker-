@extends('layouts.system')

@section('title', config('app.name', 'Document Tracker') . ' - My Profile')

@section('page_title', 'My Profile')

@section('content')
<div class="space-y-6">
    @if (session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="ml-3 text-sm text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 p-4">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <div class="ml-3 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('system.profile.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-6 text-center">
                        <div class="flex justify-center mb-4">
                            @if($user->profile_picture)
                                <img src="{{ $user->profile_picture_url }}" alt="" class="h-28 w-28 rounded-full object-cover border-4 border-indigo-100">
                            @else
                                <div class="h-28 w-28 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-3xl font-bold text-white border-4 border-indigo-100">
                                    {{ $user->initials }}
                                </div>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $user->full_name }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $user->email }}</p>
                        @if($user->office)
                            <p class="text-xs text-gray-400 mt-1">{{ $user->office->name }}</p>
                        @endif
                        <div class="mt-5">
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Change Photo</label>
                            <input type="file" name="profile_picture" accept="image/jpg,image/jpeg,image/png,image/gif,image/webp"
                                class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:transition-colors file:cursor-pointer cursor-pointer">
                            @error('profile_picture')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-900">Personal Information</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Update your profile details below.</p>
                    </div>
                    <div class="px-6 py-5 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <label for="firstname" class="block text-sm font-medium text-gray-700 mb-1.5">First Name <span class="text-red-500">*</span></label>
                                <input type="text" id="firstname" name="firstname" value="{{ old('firstname', $user->firstname) }}"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm">
                                @error('firstname') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="middlename" class="block text-sm font-medium text-gray-700 mb-1.5">Middle Name</label>
                                <input type="text" id="middlename" name="middlename" value="{{ old('middlename', $user->middlename) }}"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm">
                                @error('middlename') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="lastname" class="block text-sm font-medium text-gray-700 mb-1.5">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" id="lastname" name="lastname" value="{{ old('lastname', $user->lastname) }}"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm">
                                @error('lastname') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm">
                                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="id_number" class="block text-sm font-medium text-gray-700 mb-1.5">ID Number</label>
                                <input type="text" id="id_number" name="id_number" value="{{ old('id_number', $user->id_number) }}"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm">
                                @error('id_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1.5">Department</label>
                                <select id="department_id" name="department_id"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm">
                                    <option value="">— Select —</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="office_id" class="block text-sm font-medium text-gray-700 mb-1.5">Office</label>
                                <select id="office_id" name="office_id"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm">
                                    <option value="">— Select —</option>
                                    @foreach($offices as $off)
                                        <option value="{{ $off->id }}" {{ old('office_id', $user->office_id) == $off->id ? 'selected' : '' }}>{{ $off->name }}</option>
                                    @endforeach
                                </select>
                                @error('office_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="age" class="block text-sm font-medium text-gray-700 mb-1.5">Age</label>
                                <input type="number" id="age" name="age" value="{{ old('age', $user->age) }}" min="1" max="150"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm">
                                @error('age') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1.5">Gender</label>
                                <select id="gender" name="gender"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm">
                                    <option value="">— Select —</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    <option value="prefer-not-to-say" {{ old('gender', $user->gender) == 'prefer-not-to-say' ? 'selected' : '' }}>Prefer not to say</option>
                                </select>
                                @error('gender') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="bday" class="block text-sm font-medium text-gray-700 mb-1.5">Birthday</label>
                                <input type="date" id="bday" name="bday" value="{{ old('bday', $user->bday?->format('Y-m-d')) }}"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm">
                                @error('bday') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-5 border-t border-gray-100">
                            <a href="{{ route('system.dashboard') }}"
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
