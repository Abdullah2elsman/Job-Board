<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Http\Requests\JobRequest;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::with(['user', 'category'])->latest()->paginate(10);
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
        if ($request->user()->id !== $job->user_id) {
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
