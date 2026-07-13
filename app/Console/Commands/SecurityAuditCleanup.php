<?php

namespace App\Console\Commands;

use App\Services\SecurityAuditService;
use Illuminate\Console\Command;

class SecurityAuditCleanup extends Command
{
    protected $signature = 'security:audit-cleanup';
    protected $description = 'Purge old security audit logs';

    public function handle(SecurityAuditService $auditService): void
    {
        $deleted = $auditService->purgeOldLogs();
        $this->info("Purged {$deleted} old security log entries.");
    }
}
