<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\CandidateSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CandidateSearchController extends Controller
{
    protected CandidateSearchService $searchService;

    public function __construct(CandidateSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function search(Request $request): JsonResponse
    {
        $filters = $request->only(['keyword', 'location', 'skill']);
        $candidates = $this->searchService->searchCandidates($filters);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($candidates)
        ]);
    }
}
