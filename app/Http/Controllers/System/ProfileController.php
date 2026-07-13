<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Office;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = auth()->user()->load('department', 'office');
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $offices = Office::where('is_active', true)->orderBy('name')->get();

        return view('system.profile.index', compact('user', 'departments', 'offices'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $data = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'id_number' => ['nullable', 'string', 'max:100', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'office_id' => ['nullable', 'integer', 'exists:offices,id'],
            'age' => ['nullable', 'integer', 'min:1', 'max:150'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other', 'prefer-not-to-say'])],
            'bday' => ['nullable', 'date', 'before:today'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('local')->delete('profile-pictures/' . $user->profile_picture);
            }
            $filename = $user->id . '_' . time() . '.' . $request->file('profile_picture')->extension();
            $request->file('profile_picture')->storeAs('profile-pictures', $filename, 'local');
            $data['profile_picture'] = $filename;
        }

        $user->update($data);

        return redirect()->route('system.profile')
            ->with('success', 'Profile updated successfully.');
    }
}
