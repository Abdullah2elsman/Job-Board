<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Job $job): JsonResponse
    {
        $comment = $job->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->validated()['content'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment created successfully',
            'data' => $comment->load('user'),
        ], 201);
    }

    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        if ((int) $request->user()->id !== (int) $comment->user_id && ! $request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: You can only delete your own comment',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully',
        ]);
    }
}
