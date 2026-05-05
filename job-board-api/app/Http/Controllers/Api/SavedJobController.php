<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Services\SavedJobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavedJobController extends Controller
{
    protected SavedJobService $savedJobService;

    public function __construct(SavedJobService $savedJobService)
    {
        $this->savedJobService = $savedJobService;
    }

    public function toggle(Request $request, int $id): JsonResponse
    {
        $result = $this->savedJobService->toggleSave($request->user(), $id);

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'status' => $result['status']
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $jobs = $this->savedJobService->getSavedJobs($request->user());

        return response()->json([
            'success' => true,
            'data' => JobResource::collection($jobs)
        ]);
    }
}
