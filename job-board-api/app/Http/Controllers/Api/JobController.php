<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Job::query()
            ->where('status', 'approved')
            ->with(['category', 'employer', 'skills']);

        $query->when($request->keyword, function ($q, $v) {
            $q->where(function ($query) use ($v) {
                $query->where('title', 'like', "%$v%")
                    ->orWhere('description', 'like', "%$v%");
            });
        });

        $query->when($request->location, function ($q, $v) {
            $q->where('location', 'like', "%$v%")
                ->orWhere('work_type', 'like', "%$v%");
        });

        $query->when($request->min_salary, function ($q, $v) {
            $q->where('salary', '>=', $v);
        });

        $jobs = $query->latest()->paginate(10);

        return $this->successResponse($jobs, 'Approved jobs fetched successfully');
    }

    public function store(StoreJobRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $skills = $payload['skills'] ?? [];
        unset($payload['skills']);
        $payload['status'] = 'pending';

        $job = $request->user()->jobs()->create($payload);

        if (!empty($skills)) {
            $job->skills()->createMany(
                collect($skills)->map(fn ($skill) => ['skill_name' => $skill])->all()
            );
        }

        return $this->successResponse(
            $job->load(['category', 'employer', 'skills']),
            'Job created successfully',
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $job = Job::with(['category', 'employer', 'skills'])->find($id);

        if (!$job) {
            return $this->errorResponse('Job not found', 404);
        }

        return $this->successResponse($job, 'Job details fetched successfully');
    }

    public function update(UpdateJobRequest $request, int $id): JsonResponse
    {
        $job = Job::with('skills')->find($id);

        if (!$job) {
            return $this->errorResponse('Job not found', 404);
        }

        if ((int) $request->user()->id !== (int) $job->employer_id) {
            return $this->errorResponse('Unauthorized: You do not own this job', 403);
        }

        if ($job->status === 'approved') {
            return $this->errorResponse('Approved jobs cannot be edited', 403);
        }

        $payload = $request->validated();
        $skills = $payload['skills'] ?? null;
        unset($payload['skills']);
        $job->update($payload);

        if ($skills !== null) {
            $job->skills()->delete();
            $job->skills()->createMany(
                collect($skills)->map(fn ($skill) => ['skill_name' => $skill])->all()
            );
        }

        return $this->successResponse(
            $job->load(['category', 'employer', 'skills']),
            'Job updated successfully'
        );
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $job = Job::find($id);

        if (!$job) {
            return $this->errorResponse('Job not found', 404);
        }

        if ((int) $request->user()->id !== (int) $job->employer_id) {
            return $this->errorResponse('Unauthorized: You do not own this job', 403);
        }

        $job->delete();

        return $this->successResponse(null, 'Job deleted successfully');
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->hasRole('admin')) {
            return $this->errorResponse('Only admins can approve jobs', 403);
        }

        $job = Job::find($id);

        if (!$job) {
            return $this->errorResponse('Job not found', 404);
        }

        $job->update(['status' => 'approved']);

        return $this->successResponse($job->load(['category', 'employer', 'skills']), 'Job approved successfully');
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->hasRole('admin')) {
            return $this->errorResponse('Only admins can reject jobs', 403);
        }

        $job = Job::find($id);

        if (!$job) {
            return $this->errorResponse('Job not found', 404);
        }

        $job->update(['status' => 'rejected']);

        return $this->successResponse($job->load(['category', 'employer', 'skills']), 'Job rejected successfully');
    }

    private function successResponse($data, string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    private function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
