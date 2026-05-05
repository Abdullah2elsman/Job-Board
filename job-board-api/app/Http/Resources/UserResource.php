<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
        ];

        if ($this->role === 'candidate') {
            $data['bio'] = $this->bio;
            $data['location'] = $this->location;
            $data['skills'] = $this->skills;
            $data['linkedin_url'] = $this->linkedin_url;
            $data['resume_path'] = $this->resume_path;
        } elseif ($this->role === 'employer') {
            $data['company_name'] = $this->company_name;
            $data['company_description'] = $this->company_description;
            $data['website'] = $this->website;
            $data['location'] = $this->location;
        }

        return $data;
    }
}
