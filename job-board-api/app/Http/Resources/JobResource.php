<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'responsibilities' => $this->responsibilities,
            'requirements' => $this->requirements,
            'benefits' => $this->benefits,
            'location' => $this->location,
            'work_type' => $this->work_type,
            'experience_level' => $this->experience_level,
            'salary' => $this->salary,
            'deadline' => $this->deadline,
            'status' => $this->status,
            'views_count' => $this->views_count,
            'created_at' => $this->created_at,
            'employer' => new UserResource($this->whenLoaded('employer')),
            'category' => $this->whenLoaded('category'),
            'skills' => $this->whenLoaded('skills'),
        ];
    }
}
