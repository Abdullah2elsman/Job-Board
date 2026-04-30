<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyJobRequest;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApplicationController extends Controller
{

    public function apply(ApplyJobRequest $request, string $id): JsonResponse
    {

        $job = Job::find($id);

        if (!$job)
        {
            return response()->json(['message' => 'Job not found'], 404);
        }
        // ensure user doesn't apply for the same job twice
        $exists = Application::where('candidate_id', auth()->id())
            ->where('job_id', $id)
            ->exists();

        if ($exists)
        {
            return response()->json(['message' => 'You have already applied for this job.'], 400);
        }

        // upload the file and store its path
        $path = $request->file('resume')->store('resumes', 'public');

        $application = Application::create([
            'candidate_id' => auth()->id(),
            'job_id' => $id,
            'resume_path' => $path,
            'cover_letter' => $request->cover_letter,
            'status' => 'applied',
        ]);

        return response()->json(['message' => 'Application submitted successfully!', 'data' => $application], 201);
    }


    public function getJobApplications(string $jobId): JsonResponse
    {
        $job = Job::where('id', $jobId)
                  ->where('employer_id', auth()->id())
                  ->firstOrFail();

        $applications = $job->applications()->with('candidate')->get();

        return response()->json([
            'job_title' => $job->title,
            'data' => $applications
        ]);
    }


    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:interviewing,accepted,rejected',
        ]);

        $application = Application::findOrFail($id);

        if ($application->job->employer_id !== auth()->id())
        {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $application->update(['status' => $request->status]);

        return response()->json(['message' => 'Application status updated to ' . $request->status]);
    }

    public function getMyApplications(): JsonResponse
    {
        $applications = Application::where('candidate_id', auth()->id())->with('job')->get();

        return response()->json(['data' => $applications]);
    }
}
