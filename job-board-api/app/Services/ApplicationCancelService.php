<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ApplicationCancelService
{
    public function cancelApplication(int $applicationId, int $userId): void
    {
        $application = Application::findOrFail($applicationId);

        if ($application->candidate_id !== $userId) {
            throw ValidationException::withMessages([
                'error' => 'You are not authorized to cancel this application.'
            ]);
        }

        if ($application->resume_path) {
            Storage::disk('public')->delete($application->resume_path);
        }

        $application->delete();
    }
}
