<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class SavedJobService
{
    public function toggleSave(User $user, int $jobId): array
    {
        $hasSaved = $user->savedJobs()->where('job_id', $jobId)->exists();

        if ($hasSaved) {
            $user->savedJobs()->detach($jobId);
            return ['status' => 'removed', 'message' => 'Job removed from saved list'];
        } else {
            $user->savedJobs()->attach($jobId);
            return ['status' => 'added', 'message' => 'Job saved successfully'];
        }
    }

    public function getSavedJobs(User $user): Collection
    {
        return $user->savedJobs()->with(['employer', 'category'])->latest('saved_jobs.created_at')->get();
    }
}
