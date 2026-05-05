<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CandidateSearchService
{
    public function searchCandidates(array $filters): Collection
    {
        $query = User::where('role', 'candidate');

        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('bio', 'like', "%{$keyword}%")
                  ->orWhere('skills', 'like', "%{$keyword}%");
            });
        }

        if (!empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        if (!empty($filters['skill'])) {
            // skills column is stored as JSON
            $skill = $filters['skill'];
            $query->whereJsonContains('skills', $skill);
        }

        return $query->get();
    }
}
