<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InputSanitizeMiddleware
{
    private array $skipFields = ['password', 'password_confirmation', 'current_password', '_token'];

    public function handle(Request $request, Closure $next): Response
    {
        if (!config('security.input_validation.sanitize_all_inputs')) {
            return $next($request);
        }

        $sanitized = $this->sanitizeInput($request->all());
        $request->merge($sanitized);

        return $next($request);
    }

    private function sanitizeInput(array $input): array
    {
        $result = [];
        foreach ($input as $key => $value) {
            if (in_array($key, $this->skipFields, true)) {
                $result[$key] = $value;
                continue;
            }
            if (is_array($value)) {
                $result[$key] = $this->sanitizeInput($value);
            } elseif (is_string($value)) {
                $result[$key] = $this->sanitizeString($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    private function sanitizeString(string $value): string
    {
        $value = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $value);

        $value = preg_replace('/javascript\s*:/i', '', $value);
        $value = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $value);
        $value = preg_replace('/on\w+\s*=\s*\S+/i', '', $value);
        $value = preg_replace('/vbscript\s*:/i', '', $value);
        $value = preg_replace('/-moz-binding\s*:/i', '', $value);
        $value = preg_replace('/expression\s*\(/i', '', $value);

        if (config('security.input_validation.encode_special_chars')) {
            $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
        }

        $maxLength = config('security.input_validation.max_input_length', 10000);
        if (strlen($value) > $maxLength) {
            $value = substr($value, 0, $maxLength);
        }

        return $value;
    }
}
