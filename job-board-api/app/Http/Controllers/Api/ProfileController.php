<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function updateCandidate(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|min:2',
            'bio' => 'nullable|string|min:10',
            'location' => 'required|string',
            'skills' => 'nullable', // Can be string or array due to FormData
            'resume' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $user = $this->profileService->updateCandidateProfile($request->user(), $request->all());

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function updateEmployer(Request $request): JsonResponse
    {
        $request->validate([
            'company_name' => 'required|string|min:2',
            'company_description' => 'required|string|min:10',
            'website' => 'nullable|url',
            'location' => 'required|string',
        ]);

        $user = $this->profileService->updateEmployerProfile($request->user(), $request->all());

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user)
        ]);
    }
}
