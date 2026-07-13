<?php

namespace App\Services;

use App\Models\UserActivity;
use Illuminate\Support\Facades\Request;

class UserActivityService
{
    public function log(
        string $activity,
        ?string $description = null,
        ?array $metadata = null,
        ?int $userId = null,
        ?array $oldData = null,
        ?array $newData = null,
        ?array $geolocation = null,
    ): UserActivity {
        return UserActivity::create([
            'user_id' => $userId ?? auth()->id(),
            'activity' => $activity,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'geolocation' => $geolocation,
            'old_data' => $oldData,
            'new_data' => $newData,
        ]);
    }
}
