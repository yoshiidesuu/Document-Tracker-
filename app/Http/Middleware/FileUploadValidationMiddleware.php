<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FileUploadValidationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->hasFile('file') && ! $request->allFiles()) {
            return $next($request);
        }

        $maxSize = config('security.uploads.max_file_size', 10485760);
        $allowedExtensions = explode(',', config('security.uploads.allowed_extensions', 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv'));

        foreach ($request->allFiles() as $key => $files) {
            $files = is_array($files) ? $files : [$files];

            foreach ($files as $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }

                if ($file->getSize() > $maxSize) {
                    return response()->json([
                        'message' => "The file '{$key}' exceeds the maximum allowed size of ".($maxSize / 1048576).'MB.',
                    ], 422);
                }

                $extension = strtolower($file->getClientOriginalExtension());
                if (! in_array($extension, $allowedExtensions)) {
                    return response()->json([
                        'message' => "The file '{$key}' has an invalid extension '{$extension}'. Allowed: ".implode(', ', $allowedExtensions),
                    ], 422);
                }

                $mimeType = $file->getMimeType();
                if (! $this->isAllowedMimeType($mimeType, $extension)) {
                    return response()->json([
                        'message' => "The file '{$key}' has an invalid type.",
                    ], 422);
                }
            }
        }

        return $next($request);
    }

    private function isAllowedMimeType(string $mimeType, string $extension): bool
    {
        $mimeMap = [
            'jpg' => ['image/jpeg', 'image/jpg'],
            'jpeg' => ['image/jpeg', 'image/jpg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xls' => ['application/vnd.ms-excel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'csv' => ['text/csv', 'text/plain', 'application/csv'],
        ];

        return isset($mimeMap[$extension]) && in_array($mimeType, $mimeMap[$extension]);
    }
}
