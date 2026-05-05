<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApplicationCancelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApplicationCancelController extends Controller
{
    protected ApplicationCancelService $cancelService;

    public function __construct(ApplicationCancelService $cancelService)
    {
        $this->cancelService = $cancelService;
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $this->cancelService->cancelApplication($id, $request->user()->id);
            return response()->json([
                'success' => true,
                'message' => 'Application cancelled successfully.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel application.'
            ], 500);
        }
    }
}
