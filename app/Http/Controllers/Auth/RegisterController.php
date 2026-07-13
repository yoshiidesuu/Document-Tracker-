<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\UserActivityService;
use App\Services\EncryptionService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    public function __construct(
        private readonly UserActivityService $userActivity,
        private readonly EncryptionService $encryption,
    ) {}

    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $data = [
            'firstname' => $request->input('firstname'),
            'middlename' => $request->input('middlename'),
            'lastname' => $request->input('lastname'),
            'name' => trim(implode(' ', array_filter([
                $request->input('firstname'),
                $request->input('middlename'),
                $request->input('lastname'),
            ]))),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'password_changed_at' => now(),
            'terms_accepted_at' => now(),
            'privacy_accepted_at' => now(),
            'id_number' => $request->input('id_number'),
            'age' => $request->input('age'),
            'gender' => $request->input('gender'),
            'bday' => $request->input('bday'),
            'ip' => $request->ip(),
            'status' => 'active',
            'locked' => false,
            'banned' => false,
        ];

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures', 'local');
            $data['profile_picture'] = $path;
        }

        $geoData = $this->getGeolocation($request->ip());
        if ($geoData) {
            $data['geolocation'] = $geoData;
        }

        $user = User::create($data);

        $user->email_hash = $this->encryption->hashEmail($user->email);
        $user->save();

        event(new Registered($user));

        auth()->login($user);

        $this->userActivity->log('user_registered', "New user registered: {$user->email}", userId: $user->id, newData: $user->only(['firstname', 'lastname', 'email', 'id_number', 'gender', 'status']));

        return response()->json([
            'user' => $user->only([
                'id', 'firstname', 'middlename', 'lastname', 'name',
                'email', 'id_number', 'age', 'gender', 'bday',
                'profile_picture', 'status', 'geolocation',
            ]),
            'message' => 'Registration successful.',
        ], 201);
    }

    private function getGeolocation(?string $ip): ?array
    {
        if (!$ip || $ip === '127.0.0.1' || $ip === '::1') {
            return null;
        }

        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,lat,lon,isp");
            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['status']) && $data['status'] === 'success') {
                    return [
                        'country' => $data['country'] ?? null,
                        'region' => $data['regionName'] ?? null,
                        'city' => $data['city'] ?? null,
                        'lat' => $data['lat'] ?? null,
                        'lon' => $data['lon'] ?? null,
                        'isp' => $data['isp'] ?? null,
                    ];
                }
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }
}
