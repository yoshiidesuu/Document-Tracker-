<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function logo()
    {
        $filename = SystemSetting::get('site_logo');
        if (! $filename || ! Storage::disk('local')->exists("system/logo/$filename")) {
            return response('', 200)
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate');
        }
        $path = Storage::disk('local')->path("system/logo/$filename");

        return response()->file($path, $this->headers());
    }

    public function favicon()
    {
        $filename = SystemSetting::get('site_favicon');
        if (! $filename || ! Storage::disk('local')->exists("system/favicon/$filename")) {
            return response('', 200)
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate');
        }
        $path = Storage::disk('local')->path("system/favicon/$filename");

        return response()->file($path, $this->headers());
    }

    public function documentRightLogo()
    {
        $filename = SystemSetting::get('document_right_logo');
        if (! $filename || ! Storage::disk('local')->exists("system/document-logo-right/$filename")) {
            return response('', 200)
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate');
        }
        $path = Storage::disk('local')->path("system/document-logo-right/$filename");

        return response()->file($path, $this->headers());
    }

    public function profile(string $filename): StreamedResponse
    {
        $path = "profile-pictures/$filename";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->response($path, null, $this->headers());
    }

    private function headers(): array
    {
        return [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Content-Security-Policy' => "default-src 'none'; img-src 'self'; style-src 'unsafe-inline'",
            'Content-Disposition' => 'inline',
            'X-Robots-Tag' => 'noindex, nofollow',
        ];
    }
}
