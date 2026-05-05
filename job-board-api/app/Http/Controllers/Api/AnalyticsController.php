<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Comment;
use App\Models\Job;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('admin');
        $jobQuery = Job::query()->with(['category', 'employer'])->withCount(['applications', 'comments']);

        if (! $isAdmin) {
            $jobQuery->where('employer_id', $user->id);
        }

        $jobs = $jobQuery->latest()->get();

        $applicationsQuery = Application::query();
        $commentsQuery = Comment::query();
        $paymentsQuery = Payment::query();

        if (! $isAdmin) {
            $applicationsQuery->whereHas('job', function ($query) use ($user) {
                $query->where('employer_id', $user->id);
            });

            $commentsQuery->whereHas('job', function ($query) use ($user) {
                $query->where('employer_id', $user->id);
            });

            $paymentsQuery->where('employer_id', $user->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Analytics fetched successfully',
            'data' => [
                'totals' => [
                    'jobs' => $jobs->count(),
                    'applications' => $applicationsQuery->count(),
                    'comments' => $commentsQuery->count(),
                    'payments' => $paymentsQuery->count(),
                    'job_views' => (int) $jobs->sum('views_count'),
                    'approved_jobs' => $jobs->where('status', 'approved')->count(),
                    'pending_jobs' => $jobs->where('status', 'pending')->count(),
                    'rejected_jobs' => $jobs->where('status', 'rejected')->count(),
                ],
                'jobs' => $jobs->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'status' => $job->status,
                        'views_count' => (int) ($job->views_count ?? 0),
                        'applications_count' => $job->applications_count,
                        'comments_count' => $job->comments_count,
                        'category' => $job->category?->name,
                        'location' => $job->location,
                        'deadline' => $job->deadline,
                    ];
                }),
            ],
        ]);
    }
}
