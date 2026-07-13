<?php

namespace App\Services;

use App\Models\SecurityLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class SecurityAuditService
{
    public function log(
        string $event,
        ?string $description = null,
        ?array $metadata = null,
        ?int $userId = null,
        string $severity = 'info'
    ): SecurityLog {
        $log = SecurityLog::create([
            'user_id' => $userId ?? auth()->id(),
            'event' => $event,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'request_method' => Request::method(),
            'request_url' => Request::fullUrl(),
            'severity' => $severity,
            'metadata' => $metadata ? json_encode($metadata) : null,
        ]);

        Log::channel(config('security.audit.channel', 'security'))->log(
            $severity === 'critical' ? 'critical' : ($severity === 'warning' ? 'warning' : 'info'),
            "[{$event}] {$description}",
            [
                'user_id' => $userId ?? auth()->id(),
                'ip' => Request::ip(),
                'metadata' => $metadata,
            ]
        );

        return $log;
    }

    public function logLoginSuccess(int $userId): SecurityLog
    {
        return $this->log('login_success', 'User logged in successfully', null, $userId, 'info');
    }

    public function logLoginFailed(string $email, ?string $reason = null): SecurityLog
    {
        return $this->log('login_failed', "Failed login attempt for {$email}".($reason ? ": {$reason}" : ''), ['email' => $email], severity: 'warning');
    }

    public function logPasswordChange(int $userId): SecurityLog
    {
        return $this->log('password_change', 'User changed password', null, $userId, 'info');
    }

    public function logAccountLockout(string $email): SecurityLog
    {
        return $this->log('account_locked', "Account locked for {$email}", ['email' => $email], severity: 'warning');
    }

    public function logSuspiciousActivity(string $description, array $metadata = []): SecurityLog
    {
        return $this->log('suspicious_activity', $description, $metadata, severity: 'warning');
    }

    public function logDataExport(int $userId, string $exportType): SecurityLog
    {
        return $this->log('data_export', "User exported {$exportType}", ['export_type' => $exportType], $userId, 'info');
    }

    public function logPermissionDenied(int $userId, string $resource): SecurityLog
    {
        return $this->log('permission_denied', "Access denied to {$resource}", ['resource' => $resource], $userId, 'warning');
    }

    public function logBreachNotification(string $type, string $description): SecurityLog
    {
        return $this->log('breach_'.$type, $description, severity: 'critical');
    }

    public function logMfaEvent(int $userId, string $action, bool $success): SecurityLog
    {
        return $this->log(
            $success ? 'mfa_success' : 'mfa_failed',
            "MFA {$action}: ".($success ? 'success' : 'failed'),
            ['mfa_action' => $action],
            $userId,
            $success ? 'info' : 'warning'
        );
    }

    public function purgeOldLogs(): int
    {
        $retentionDays = config('security.audit.retention_days', 365);

        return SecurityLog::where('created_at', '<', now()->subDays($retentionDays))->delete();
    }
}
