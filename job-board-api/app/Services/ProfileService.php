<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function updateCandidateProfile(User $user, array $data): User
    {
        if (isset($data['skills']) && is_string($data['skills'])) {
            $data['skills'] = json_decode($data['skills'], true);
        }

        if (isset($data['resume']) && $data['resume'] instanceof UploadedFile) {
            if ($user->resume_path) {
                Storage::disk('public')->delete($user->resume_path);
            }
            $data['resume_path'] = $data['resume']->store('profiles/resumes', 'public');
            unset($data['resume']);
        }

        $user->update($data);

        return $user;
    }

    public function updateEmployerProfile(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }
}
