<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Http\Requests\JobRequest;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
{
    $query = Job::query()->where('status', 'approved');

    // 1. بحث الكلمة المفتاحية (Laravel)
    $query->when($request->keyword, function ($q, $v) {
        $q->where(function ($query) use ($v) {
            $query->where('title', 'like', "%$v%")
                  ->orWhere('description', 'like', "%$v%");
        });
    });

    // 2. بحث المكان (استخدم like عشان لو كتب Cairo بس يشتغل)
    $query->when($request->location, function ($q, $v) {
        $q->where('location', 'like', "%$v%");
    });

    // 3. فلتر الراتب (تأكد من اسم العمود salary_min كما في ملفك)
    $query->when($request->min_salary, function ($q, $v) {
        $q->where('salary_min', '>=', $v);
    });

    $jobs = $query->with(['employer', 'category'])->latest()->paginate(10);

    return response()->json($jobs);
}

    public function store(JobRequest $request)
    {
        $job = $request->user()->jobs()->create($request->validated());

        return response()->json([
            'message' => 'Job created successfully',
            'job' => $job->load('category')
        ], 201);
    }

    public function update(JobRequest $request, Job $job)
    {
        if ($request->user()->id !== $job->user_id)
        {
            return response()->json(['message' => 'Unauthorized: You do not own this job'], 403);
        }

        $job->update($request->validated());

        return response()->json([
            'message' => 'Job updated successfully',
            'job' => $job->load('category')
        ]);
    }

    public function destroy(Request $request, Job $job)
    {
        if ($request->user()->id !== $job->user_id)
        {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $job->delete();

        return response()->json([
            'message' => 'Job deleted successfully'
        ], 200);
    }

    public function show(Job $job)
    {
        return response()->json($job->load(['user', 'category', 'comments']));
    }
}
